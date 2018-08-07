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
     * Horários pertencem ao pai dessa trait.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function schedules();

	/**
	 * Agenda um horário.
	 *
	 * @param string|Carbon\Carbon $start_at	Data em que será agendado, pode ser em string ou em numa classe Carbon.
	 * @param string|Carbon\Carbon|int $end_at   Data em que acabada esse agendamento, pode ser em string, ou numa classe Carbon
	 *                                    ou em int(sendo considerado os minutos de duração).
	 * @param int $status	Status desse horário ao ser agendado.
	 */
	public function addSchedule($start_at, $end_at = null, $status = null);

	/**
	 * Exibe uma lista dos horários do dia de hoje.
	 *
	 * @return [type] [description]
	 */
	public function availableToday();

	/**
	 * Lista os horários livres em um determinado dia.
	 *
	 * @param  string|Carbon\Carbon $date Data para o qual ele irá fazer a busca.
	 * @return [type]       [description]
	 */
	public function availableOn($date);
}