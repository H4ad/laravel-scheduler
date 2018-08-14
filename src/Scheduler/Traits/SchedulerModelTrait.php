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
use H4ad\Scheduler\Exceptions\EndCantBeforeStart;
use H4ad\Scheduler\Exceptions\CantAddWithSameStartAt;

/**
 * Implementação do contrato [SchedulerModelTrait].
 * @see \H4ad\Scheduler\Contracts\SchedulerModelTrait
 */
trait SchedulerModelTrait
{
	/**
     * Define an inverse one-to-one or many relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $ownerKey
     * @param  string  $relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	abstract public function belongsTo($related, $foreignKey = null, $ownerKey = null, $relation = null);

	/**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
	abstract public function getKey();

	/**
     * Retorna apenas os horários que possuem o mesmo [model_type] do [parent] dessa [trait].
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function schedules()
	{
		return $this->belongsTo(Config::get('scheduler.schedules_table'), 'model_id');
	}

	/**
	 * Agenda um horário para esta model.
	 *
	 * @param string|\Carbon\Carbon $start_at	Data em que será agendado, pode ser em string ou em numa classe Carbon.
	 * @param string|\Carbon\Carbon|int $end_at   Data em que acabada esse agendamento, pode ser em string, ou numa classe Carbon
	 *                                           ou em int(sendo considerado os minutos de duração).
	 * @param int $status	Status desse horário ao ser agendado.
	 * @return \H4ad\Scheduler\Models\Schedule
	 *
	 * @throws \H4ad\Scheduler\Exceptions\CantAddWithoutEnd
	 * @throws \H4ad\Scheduler\Exceptions\CantAddWithSameStartAt
	 * @throws \H4ad\Scheduler\Exceptions\EndCantBeforeStart
	 */
	public function addSchedule($start_at, $end_at = null, $status = null)
	{
		if(!Config::get('scheduler.enable_schedule_without_end') && is_null($end_at))
			throw new CantAddWithoutEnd;

		$start_at  = $this->parseToCarbon($start_at);
		$end_at = $this->parseToCarbon($end_at, $start_at);

		if(Scheduler::hasScheduleBetween(self::class, $start_at, $end_at ?? $start_at))
			throw new CantAddWithSameStartAt;

		if($start_at->greaterThan($end_at) && !is_null($end_at))
			throw new EndCantBeforeStart;

		$model_id = $this->getKey();
		$model_type = self::class;

		return Schedule::create(compact('start_at', 'end_at', 'status', 'model_id', 'model_type'));
	}

	/**
	 * Faz um parse na data e retorna uma instância em Carbon.
	 *
	 * @param  string|int $date Data final que será transformada numa instancia Carbon.
	 * @param  \Carbon\Carbon $reference Data de referencia quando o [date] é inteiro.
	 * @return \Carbon\Carbon
	 */
	public function parseToCarbon($date, $reference = null)
	{
		if(is_string($date))
			return Carbon::parse($date);

		if(is_int($date))
			return Carbon::parse($reference->toDateTimeString())->addMinutes($date);

		return $date;
	}

	/**
	 * Faz um parse e retorna um Schedule.
	 *
	 * @param  \Carbon\Carbon|string|int $value Valor que representará a data ou o id a ser buscado.
	 * @return H4ad\Scheduler\Models\Schedule|null
	 */
	public function parseToSchedule($value)
	{
		if(is_int($value))
			return Schedule::find($value);

		if(is_string($value) || $value instanceof Carbon)
			return Schedule::byStartAt($value)->first();

		return null;
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