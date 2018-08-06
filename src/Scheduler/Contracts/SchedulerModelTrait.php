<?php namespace H4ad\Scheduler\Contracts;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

interface SchedulerModelInterface
{
	/**
     * Horários pertencem ao pai.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function schedules();

	public function addSchedule

}