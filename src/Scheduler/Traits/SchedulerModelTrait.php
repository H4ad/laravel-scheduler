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
use Illuminate\Support\Facades\Config;
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
     * Retorna apenas os horários que possuem o mesmo [model_type] do [parent] dessa [trait].
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function schedules()
	{
		return $this->belongsTo(Config::get('schedules_table'), 'model_id')->where('model_type', get_parent_class($this));
	}

	/**
	 * Agenda um horário para esta model.
	 *
	 * @param string|Carbon\Carbon $start_at	Data em que será agendado, pode ser em string ou em numa classe Carbon.
	 * @param string|Carbon\Carbon|int $end_at   Data em que acabada esse agendamento, pode ser em string, ou numa classe Carbon
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
		if(!Config::get('enable_schedule_without_end') && is_null($end_at))
			throw new CantAddWithoutEnd;

		if(Config::get('enable_schedule_conflict') && !is_null(Schedule::where('start_at', $start_at)->first()))
			throw new CantAddWithSameStartAt;

		if(is_string($start_at))
			$start_at = Carbon::parse($start_at);

		if(is_string($end_at))
			$end_at = Carbon::parse($end_at);

		if(is_int($end_at))
			$end_at = $start_at->addMinutes($end_at);

		if($start_at->ls($end_at))
			throw new EndCantBeforeStart;

		$model_id = $this->getKey();
		$model_type = get_parent_class($this);

		return Schedule::create(compact('start_at', 'end_at', 'status', 'model_id', 'model_type'));
	}
}