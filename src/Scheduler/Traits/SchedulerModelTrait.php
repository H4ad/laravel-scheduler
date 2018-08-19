<?php namespace H4ad\Scheduler\Traits;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

use Carbon\Carbon;
use H4ad\Scheduler\Models\Schedule;
use H4ad\Scheduler\Facades\Scheduler;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use H4ad\Scheduler\Exceptions\DoesNotBelong;
use H4ad\Scheduler\Exceptions\ModelNotFound;
use H4ad\Scheduler\Exceptions\CantRemoveByDate;
use H4ad\Scheduler\Exceptions\CantAddWithoutEnd;
use H4ad\Scheduler\Exceptions\IntInvalidArgument;
use H4ad\Scheduler\Exceptions\EndCantBeforeStart;
use H4ad\Scheduler\Exceptions\CantAddWithSameStartAt;

/**
 * Implementação do contrato [SchedulerModelTrait].
 * @see \H4ad\Scheduler\Contracts\SchedulerModelTrait
 */
trait SchedulerModelTrait
{
	/**
     * Define a one-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    abstract public function hasMany($related, $foreignKey = null, $localKey = null);

	/**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
	abstract public function getKey();

	/**
     * Retorna apenas os horários que possuem o mesmo [model_type] do [parent] dessa [trait].
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function schedules()
	{
		return $this->hasMany(Config::get('scheduler.schedules_table'), 'model_id');
	}

	/**
	 * Agenda um horário para esta model.
	 *
	 * @param  \Carbon\Carbon|string $start_at	Data em que será agendado, pode ser em string ou em numa classe Carbon.
	 * @param  \Carbon\Carbon|string|int|null $end_at   Data em que acabada esse agendamento, pode ser em string, ou numa classe Carbon
	 *                                                  ou em int(sendo considerado os minutos de duração).
	 * @param  int|null $status	Status desse horário ao ser agendado.
	 * @return \H4ad\Scheduler\Models\Schedule
	 *
	 * @throws \H4ad\Scheduler\Exceptions\CantAddWithoutEnd
	 * @throws \H4ad\Scheduler\Exceptions\EndCantBeforeStart
	 * @throws \H4ad\Scheduler\Exceptions\CantAddWithSameStartAt
	 */
	public function addSchedule($start_at, $end_at = null, $status = null)
	{
		if(!Config::get('scheduler.enable_schedule_without_end') && is_null($end_at))
			throw new CantAddWithoutEnd;

		$start_at  = $this->parseToCarbon($start_at);

		if(!is_null($end_at)) {
			$end_at = $this->parseToCarbon($end_at, $start_at);

			if($start_at->greaterThan($end_at))
				throw new EndCantBeforeStart;
		}

		if(Scheduler::hasScheduleBetween(self::class, $start_at, $end_at ?? $start_at))
			throw new CantAddWithSameStartAt;

		$model_id = $this->getKey();
		$model_type = self::class;

		return Schedule::create(compact('start_at', 'end_at', 'status', 'model_id', 'model_type'));
	}

	/**
	 * Exibe uma lista dos horários do dia de hoje.
	 *
	 * @param  int    $duration Serve para facilitar na hora de buscar horários livres
	 *                          que precisem ter uma certa duração.
     * @param \Carbon\Carbon|null $openingTime Serve como referencia para buscar horários livres.
     *                                         Se for nulo, ele busca a referencia da config.
	 * @return array
	 */
	public function availableToday($duration = 0, $openingTime = null)
	{
		return Scheduler::availableToday(self::class, $duration, $openingTime);
	}

	/**
	 * Lista os horários livres em um determinado dia.
	 *
	 * @param  string|\Carbon\Carbon $date Data para o qual ele irá fazer a busca.
	 * @param  int    $duration Serve para facilitar na hora de buscar horários livres
	 *                          que precisem ter uma certa duração.
     * @param \Carbon\Carbon|null $openingTime Serve como referencia para buscar horários livres.
     *                                         Se for nulo, ele busca a referencia da config.
	 * @return array
	 */
	public function availableOn($date, $duration = 0, $openingTime = null)
	{
		return Scheduler::availableOn(self::class, $date, $duration, $openingTime);
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
	 * Remove um horário agendado pelo seu ID ou pelo horário em que foi marcado.
	 * Caso a configuração "enable_schedule_conflict" estiver desabilitada, será lançado uma exceção
	 * se for tentado remover um horário agendado pela data de quando foi marcado.
	 *
	 * @param  int|string|\Carbon\Carbon $schedule    Horário agendado.
	 * @return bool|null
	 *
	 * @throws \H4ad\Scheduler\Exceptions\DoesNotBelong
	 * @throws \H4ad\Scheduler\Exceptions\CantRemoveByDate
	 * @throws \H4ad\Scheduler\Exceptions\ModelNotFound
	 */
	public function removeSchedule($schedule)
	{
		if(!Config::get('scheduler.enable_schedule_conflict') && !is_int($schedule))
			throw new CantRemoveByDate;

		$schedule = $this->parseToSchedule($schedule);

		if(!($schedule instanceof Model))
			throw (new ModelNotFound)->setValues(Schedule::class);

		if($schedule->model_type != self::class || $schedule->model_id != $this->getKey())
			throw new DoesNotBelong;

		return $schedule->delete();
	}
}