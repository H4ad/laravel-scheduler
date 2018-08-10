<?php namespace H4ad\Scheduler\Traits;

use Illuminate\Support\Facades\Config;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
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
}