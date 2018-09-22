<?php namespace H4ad\Scheduler;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

use Closure;
use Carbon\Carbon;
use H4ad\Scheduler\Models\Schedule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Traits\Macroable;
use H4ad\Scheduler\Models\ScheduleStatus;
use H4ad\Scheduler\Exceptions\CantAddWithoutEnd;
use H4ad\Scheduler\Exceptions\IntInvalidArgument;
use H4ad\Scheduler\Exceptions\EndCantBeforeStart;
use H4ad\Scheduler\Exceptions\CantAddWithSameStartAt;

class Scheduler
{
    use Macroable;

    /**
     * Query a ser executada.
     *
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $query;

    /**
     * Tipo de model usada para validar.
     *
     * @var string
     */
    protected $model_type;

    /**
     * Closure de boot.
     *
     * @var Closure
     */
    protected static $boot;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->query = Schedule::query();
    }

    /**
     * Define método executado toda vez que é gerada uma nova instância do Scheduler.
     * Há apenas um unico parâmetro passado para a função que é o query.
     *
     * @param  \Closure $boot_method
     * @return void
     */
    public function init(Closure $boot)
    {
        static::$boot = $boot;
    }

    /**
     * Ignora uma lista de ids da model.
     *
     * @param  array  $model_ids
     * @return $this
     */
    public function avoid(array $model_ids)
    {
        $this->query->whereNotIn('model_id', $model_ids);

        return $this;
    }

    /**
     * Verifica se há conflito de horários registrados em um intervalo de tempo.
     *
     * @param  \Carbon\Carbon|string $start_at
     * @param  \Carbon\Carbon|string $end_at
     * @return boolean
     */
    public function hasConflict($start_at, $end_at)
    {
        if(!Config::get('scheduler.enable_schedule_conflict'))
            return false;

        $this->byModel();
        $this->whereBetween($start_at, $end_at);

        return $this->query->exists();
    }

    /**
     * Busca horários que estejam dentro um intervalo de tempo.
     *
     * @param  \Carbon\Carbon|string $start_at
     * @param  \Carbon\Carbon|string $end_at
     * @return $this
     */
    public function whereBetween($start_at, $end_at)
    {
        $this->query->where('start_at', '>=', $start_at)
              ->where('end_at', '<=', $end_at);

        return $this;
    }

    /**
     * Define o tipo de model buscada.
     *
     * @param  string|null $model_type
     * @return $this
     */
    public function byModel()
    {
        $this->query->where('model_type', $this->getModelType());

        return $this;
    }

    /**
     * Seta a model padrão.
     *
     * @param string $model_type
     * @return $this
     */
    public function setModelType(string $model_type)
    {
        $this->model_type = $model_type;

        return $this;
    }

    /**
     * Retorna a model type.
     *
     * @return string
     */
    public function getModelType()
    {
        return $this->model_type ?? Config::get('scheduler.default_model');
    }
    /**
     * Retorna os horários disponiveis hoje para uma determinada model.
     * .
     * @param  int $duration Serve para facilitar na hora de buscar horários livres que precisem ter uma certa duração.
     * @param \Carbon\Carbon|null $openingTime Serve como referencia para buscar horários livres. Se for nulo, ele busca a referencia da config.
     * @return array
     */
    public function availableToday(int $duration, Carbon $openingTime = null)
    {
        return $this->availableOn(Carbon::today(), $duration, $openingTime);
    }

    /**
     * Retorna os horários disponiveis em um determinado dia para uma certa model.
     *
     * @param  \Carbon\Carbon $today Data para o qual ele irá fazer a busca.
     * @param  int $durationMinutes Serve para facilitar na hora de buscar horários livres que precisem ter uma certa duração.
     * @param  \Carbon\Carbon|null $openingTime Serve como referencia para buscar horários livres. Se for nulo, ele busca a referencia da config.
     * @return array
     */
    public function availableOn(Carbon $today, int $durationMinutes, Carbon $openingTime = null)
    {
        $openingTime = $openingTime ?? Carbon::parse(Config::get('scheduler.opening_time'))->setDateFrom($today);
        $closingTime = Carbon::parse(Config::get('scheduler.closing_time'))->setDateFrom($today);

        $livres = [];
        $today = Carbon::parse($today->toDateString());
        while($openingTime <= $closingTime)
        {
            $add = true;

            $opening = Carbon::parse($openingTime->toDateTimeString());
            $closing = Carbon::parse($openingTime->toDateTimeString())->addMinutes($durationMinutes);

            foreach ($this->query->orderBy('start_at', 'DESC')->cursor() as $schedule) {
                $start = Carbon::parse($schedule->start_at);
                $begin = Carbon::parse($start->toDateString());

                if($begin->greaterThan($today))
                    break;

                if($begin->notEqualTo($today))
                    continue;

                $end = Carbon::parse($schedule->end_at);

                if($this->isShouldntAdd($opening, $closing, $start, $end))
                    $add = false;
            }

            if($add && $closing->lessThanOrEqualTo($closingTime))
                $livres[] = [
                    'start_at' => $opening,
                    'end_at' => $closing
                ];

            $openingTime->addMinutes($durationMinutes);
        }

        return $livres;
    }

    /**
     * Verifica se ele não deve ser adicionado ao array de horários livres.
     *
     * @param  \Carbon\Carbon  $opening
     * @param  \Carbon\Carbon  $closing
     * @param  \Carbon\Carbon  $start
     * @param  \Carbon\Carbon  $end
     * @return boolean
     */
    private function isShouldntAdd(Carbon $opening, Carbon $closing, Carbon $start, Carbon $end)
    {
        return $start <= $opening && $end >= $closing;
    }

    /**
     * Valida e retorna os dados formatados de forma correta em um [array].
     *
     * @param  \Carbon\Carbon|string $start_at  Data em que será agendado, pode ser em string ou em numa classe Carbon.
     * @param  \Carbon\Carbon|string|int|null $end_at   Data em que acabada esse agendamento, pode ser em string, ou numa classe Carbon ou em int(sendo considerado os minutos de duração).
     * @param  int|null $status Status desse horário ao ser agendado.
     * @return array
     *
     * @throws \H4ad\Scheduler\Exceptions\CantAddWithoutEnd
     * @throws \H4ad\Scheduler\Exceptions\EndCantBeforeStart
     * @throws \H4ad\Scheduler\Exceptions\CantAddWithSameStartAt
     */
    public function validateSchedule($start_at, $end_at = null, int $status = null)
    {
        if(!Config::get('scheduler.enable_schedule_without_end') && is_null($end_at))
            throw new CantAddWithoutEnd;

        $start_at  = $this->parseToCarbon($start_at);

        if(isset($end_at)) {
            $end_at = $this->parseToCarbon($end_at, $start_at);

            if($start_at->greaterThan($end_at))
                throw new EndCantBeforeStart;
        }

        if(isset($status))
            ScheduleStatus::findOrFail($status);

        if($this->hasConflict($start_at, $end_at ?? $start_at))
            throw new CantAddWithSameStartAt;

        $model_type = $this->getModelType();

        return compact('model_type', 'start_at', 'end_at', 'status');
    }

    /**
     * Faz um parse na data e retorna uma instância em Carbon.
     *
     * @param  \Carbon\Carbon|string|int $date Data final que será transformada numa instancia Carbon.
     * @param  \Carbon\Carbon $reference Data de referencia quando o [date] é inteiro.
     * @return \Carbon\Carbon
     *
     * @throws \H4ad\Scheduler\Exceptions\IntInvalidArgument
     */
    public function parseToCarbon($date, $reference = null)
    {
        if($date instanceof Carbon)
            return $date;

        if(is_string($date))
            return Carbon::parse($date);

        if(is_int($date) && !is_null($reference))
            return Carbon::parse($reference->toDateTimeString())->addMinutes($date);

        throw new IntInvalidArgument;
    }

    /**
     * Faz um parse e retorna um Schedule.
     *
     * @param  \Carbon\Carbon|string|int $value Valor que representará a data ou o id a ser buscado.
     * @return \H4ad\Scheduler\Models\Schedule|null
     */
    public function parseToSchedule($value)
    {
        if(is_int($value))
            return Schedule::find($value);

        return Schedule::byStartAt($value)->first();
   }

    /**
     * Retorna uma instância do Scheduler.
     *
     * @return $this
     */
    public function newInstance()
    {
        return clone $this;
    }

   /**
    * Callback apos chamar um clone.
    *
    * @return void
    */
    public function __clone()
    {
        $this->query = Schedule::query();

        if(isset(static::$boot))
            (static::$boot)($this);
    }
}