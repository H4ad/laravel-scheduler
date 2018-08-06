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
     * Horários pertencem ao pai.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function schedules()
	{
		return $this->belongsTo(Config::get('schedules_table'), 'model_id');
	}
}