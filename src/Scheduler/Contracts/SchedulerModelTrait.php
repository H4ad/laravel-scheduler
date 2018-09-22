<?php namespace H4ad\Scheduler\Contracts;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

use Carbon\Carbon;

interface SchedulerModelInterface
{
	/**
     * Define a one-to-one relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    abstract public function hasOne($related, $foreignKey = null, $localKey = null);

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
	public function schedules();

	/**
	 * Agenda um horário para esta model.
	 *
	 * @param  \Carbon\Carbon|string $start_at	Data em que será agendado, pode ser em string ou em numa classe Carbon.
	 * @param  \Carbon\Carbon|string|int|null $end_at   Data em que acabada esse agendamento, pode ser em string, ou numa classe Carbon ou em int(sendo considerado os minutos de duração).
	 * @param  int|null $status	Status desse horário ao ser agendado.
	 * @param  array|null $data Informações opcionais que podem ser anexadas ao horário cadastrado.
	 * @return \H4ad\Scheduler\Models\Schedule
	 */
	public function addSchedule($start_at, $end_at = null, int $status = null, array $data = null);

	/**
	 * Remove um horário agendado pelo seu ID ou pelo horário em que foi marcado.
	 * Caso a configuração "enable_schedule_conflict" estiver desabilitada, será lançado uma exceção
	 * se for tentado remover um horário agendado pela data de quando foi marcado.
	 *
	 * @param  \Carbon\Carbon|string|int $schedule
	 * @return boolean|null
	 *
	 * @throws \H4ad\Scheduler\Exceptions\DoesNotBelong
	 * @throws \H4ad\Scheduler\Exceptions\CantRemoveByDate
	 * @throws \H4ad\Scheduler\Exceptions\ModelNotFound
	 */
	public function removeSchedule($schedule);

	/**
     * Retorna os horários disponiveis hoje para uma determinada model.
     * .
     * @param  int $duration Serve para facilitar na hora de buscar horários livres que precisem ter uma certa duração.
     * @param  \Carbon\Carbon|null $openingTime Serve como referencia para buscar horários livres. Se for nulo, ele busca a referencia da config.
     * @return array
     */
    public function availableToday(int $duration, Carbon $openingTime = null);

    /**
     * Retorna os horários disponiveis em um determinado dia para uma certa model.
     *
     * @param  \Carbon\Carbon $today Data para o qual ele irá fazer a busca.
     * @param  int $durationMinutes Serve para facilitar na hora de buscar horários livres que precisem ter uma certa duração.
     * @param  \Carbon\Carbon|null $openingTime Serve como referencia para buscar horários livres. Se for nulo, ele busca a referencia da config.
     * @return array
     */
    public function availableOn(Carbon $today, int $durationMinutes, Carbon $openingTime = null);
}