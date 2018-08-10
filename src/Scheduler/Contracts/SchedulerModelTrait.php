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
	 * @return \H4ad\Scheduler\Models\Schedule
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
	 * @param  int    $duration Serve para facilitar na hora de buscar horários livres
	 *                          que precisem ter uma certa duração.
	 * @return [type]       [description]
	 */
	public function availableOn($date, $duration = null);

	/**
	 * Remove um horário agendado pelo seu ID ou pelo horário em que foi marcado.
	 * Caso a configuração "enable_schedule_conflict" estiver desabilitada, será lançado uma exceção
	 * se for tentado remover um horário agendado pela data de quando foi marcado.
	 *
	 * @param  int|string $schedule    Horário agendado.
	 * @return bool|null
	 *
	 * @throws \H4ad\Scheduler\Exceptions\DoesNotBelong
	 * @throws \H4ad\Scheduler\Exceptions\CantRemoveByDate
	 */
	public function removeSchedule($schedule);
}