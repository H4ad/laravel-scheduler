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
	public function schedules();

	/**
	 * Agenda um horário para esta model.
	 *
	 * @param string|\Carbon\Carbon $start_at	Data em que será agendado, pode ser em string ou em numa classe Carbon.
	 * @param string|\Carbon\Carbon|int $end_at   Data em que acabada esse agendamento, pode ser em string, ou numa classe Carbon
	 *                                    ou em int(sendo considerado os minutos de duração).
	 * @param int $status	Status desse horário ao ser agendado.
	 * @return \H4ad\Scheduler\Models\Schedule
	 *
	 * @throws \H4ad\Scheduler\Exceptions\CantAddWithoutEnd
	 * @throws \H4ad\Scheduler\Exceptions\CantAddWithSameStartAt
	 * @throws \H4ad\Scheduler\Exceptions\EndCantBeforeStart
	 */
	public function addSchedule($start_at, $end_at = null, $status = null);

	/**
	 * Exibe uma lista dos horários do dia de hoje.
	 *
	 * @param  int    $duration Serve para facilitar na hora de buscar horários livres
	 *                          que precisem ter uma certa duração.
     * @param \Carbon\Carbon|null $openingReference Serve como referencia para buscar horários livres.
     *                                         Se for nulo, ele busca a referencia da config.
	 * @return array
	 */
	public function availableToday($duration = 0, $openingReference = null);

	/**
	 * Lista os horários livres em um determinado dia.
	 *
	 * @param  string|\Carbon\Carbon $date Data para o qual ele irá fazer a busca.
	 * @param  int    $duration Serve para facilitar na hora de buscar horários livres
	 *                          que precisem ter uma certa duração.
     * @param \Carbon\Carbon|null $openingReference Serve como referencia para buscar horários livres.
     *                                         Se for nulo, ele busca a referencia da config.
	 * @return array
	 */
	public function availableOn($date, $duration = 0, $openingReference = null);

	/**
	 * Faz um parse na data e retorna uma instância em Carbon.
	 *
	 * @param  \Carbon\Carbon|string|int $date Data final que será transformada numa instancia Carbon.
	 * @param  \Carbon\Carbon $reference Data de referencia quando o [date] é inteiro.
	 * @return \Carbon\Carbon
	 *
	 * @throws \H4ad\Scheduler\Exceptions\IntInvalidArgument
	 */
	public function parseToCarbon($date, $reference = null);

	/**
	 * Faz um parse e retorna um Schedule.
	 *
	 * @param  \Carbon\Carbon|string|int $value Valor que representará a data ou o id a ser buscado.
	 * @return \H4ad\Scheduler\Models\Schedule|null
	 */
	public function parseToSchedule($value);

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
	public function removeSchedule($schedule);
}