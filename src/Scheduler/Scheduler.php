<?php namespace H4ad\Scheduler;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

use Illuminate\Support\Carbon;
use H4ad\Scheduler\Models\Schedule;
use Illuminate\Support\Facades\Config;

class Scheduler
{
    /**
     * Laravel application
     *
     * @var \Illuminate\Foundation\Application
     */
    public $app;

    /**
     * Create a new confide instance.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Escopo de uma consulta que busca horarios pela data de início.
     *
     * @param string $model_type
     * @param string|\Carbon\Carbon $start_at
     * @param string|\Carbon\Carbon $end_at
     * @return bool
     */
    public function hasScheduleBetween($model_type, $start_at, $end_at)
    {
        if(!Config::get('scheduler.enable_schedule_conflict'))
            return false;

        return !is_null(
            Schedule::latest()
                ->where('model_type', $model_type)
                ->where('start_at', '>=', $start_at)
                ->where('end_at', '<=', $end_at)
                ->first()
        );
    }

    /**
     * Retorna os horários disponiveis hoje para uma determinada model.
     * .
     * @param  string  $model_type Tipo da model
     * @param  int    $duration Serve para facilitar na hora de buscar horários livres
     *                          que precisem ter uma certa duração.
     * @param \Carbon\Carbon|null $openingTime Serve como referencia para buscar horários livres.
     *                                         Se for nulo, ele busca a referencia da config.
     * @return array
     */
    public function availableToday($model_type, $duration, $openingTime = null)
    {
        return $this->availableOn($model_type, Carbon::now(), $duration, $openingTime);
    }

    /**
     * Retorna os horários disponiveis em um determinado dia para uma certa model.
     *
     * @param  string  $model_type Tipo da model
     * @param  \Carbon\Carbon $today Data para o qual ele irá fazer a busca.
     * @param  int    $durationMinutes Serve para facilitar na hora de buscar horários livres
     *                          que precisem ter uma certa duração.
     * @param \Carbon\Carbon|null $openingTime Serve como referencia para buscar horários livres.
     *                                         Se for nulo, ele busca a referencia da config.
     * @return array
     */
    public function availableOn($model_type, $today, $durationMinutes, $openingTime = null)
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

            foreach (Schedule::where('model_type', $model_type)->orderBy('start_at', 'DESC')->cursor() as $schedule) {
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
    private function isShouldntAdd($opening, $closing, $start, $end)
    {
        return $start <= $opening && $end >= $closing;
    }
}