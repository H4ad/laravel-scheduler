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
		return $this->belongsTo(Config::get('scheduler.schedules_table'), 'model_id')->where('model_type', self::class);
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

		if(is_string($start_at))
			$start_at = Carbon::parse($start_at);

		if(is_string($end_at))
			$end_at = Carbon::parse($end_at);

		if(is_int($end_at))
			$end_at = Carbon::parse($start_at->toDateTimeString())->addMinutes($end_at);

		if(Config::get('scheduler.enable_schedule_conflict'))
			if(Scheduler::hasScheduleBetween($start_at, $end_at ?? $start_at))
				throw new CantAddWithSameStartAt;

		if($start_at->greaterThan($end_at) && !is_null($end_at))
			throw new EndCantBeforeStart;

		$model_id = $this->getKey();
		$model_type = self::class;

		return Schedule::create(compact('start_at', 'end_at', 'status', 'model_id', 'model_type'));
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

		if(is_int($schedule))
			$schedule = Schedule::find($schedule);

		if(is_string($schedule) || $schedule instanceof Carbon)
			$schedule = Schedule::byStartAt($schedule)->first();

		if(!($schedule instanceof Model))
			throw (new ModelNotFound)->setValues(Schedule::class);

		if($schedule->model_type != self::class)
			throw new DoesNotBelong;

		return $schedule->delete();
	}
}