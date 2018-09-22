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

/**
 * Implementação do contrato [SchedulerModelTrait].
 * @see \H4ad\Scheduler\Contracts\SchedulerModelTrait
 */
trait SchedulerModelTrait
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
     * Retorna apenas o horário que possui o mesmo [model_id] do [parent] dessa [trait].
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
	public function schedules()
	{
		return $this->hasOne(Config::get('scheduler.schedules_table'), 'model_id');
	}

	/**
	 * Agenda um horário para esta model.
	 *
	 * @param  \Carbon\Carbon|string $start_at Data em que será agendado, pode ser em string ou em numa classe Carbon.
	 * @param  \Carbon\Carbon|string|int|null $end_at Data em que acabada esse agendamento, pode ser em string, ou numa classe Carbon ou em int(sendo considerado os minutos de duração).
	 * @param  int|null $status	Status desse horário ao ser agendado.
	 * @param  array|null $data Informações opcionais que podem ser anexadas ao horário cadastrado.
	 * @return \H4ad\Scheduler\Models\Schedule
	 */
	public function addSchedule($start_at, $end_at = null, int $status = null, array $data = null)
	{
		$schedule = Scheduler::setModelType(self::class)->byModel()->validateSchedule($start_at, $end_at, $status);

		$schedule['model_id'] = $this->getKey();
		$schedule['data'] = $data;

		return Schedule::create($schedule);
	}

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
	public function removeSchedule($schedule)
	{
		if(!Config::get('scheduler.enable_schedule_conflict') && !is_int($schedule))
			throw new CantRemoveByDate;

		$schedule = Scheduler::parseToSchedule($schedule);

		if(!($schedule instanceof Model))
			throw (new ModelNotFound)->setValues(Schedule::class);

		if($schedule->model_type != self::class || $schedule->model_id != $this->getKey())
			throw new DoesNotBelong;

		return $schedule->delete();
	}

	/**
     * Retorna os horários disponiveis hoje para uma determinada model.
     * .
     * @param  int $duration Serve para facilitar na hora de buscar horários livres que precisem ter uma certa duração.
     * @param \Carbon\Carbon|null $openingTime Serve como referencia para buscar horários livres. Se for nulo, ele busca a referencia da config.
     * @return array
     */
    public function availableToday(int $duration, Carbon $openingTime = null)
    {
    	return Scheduler::byModel(self::class)->availableToday($duration, $openingTime);
    }

    /**
     * Retorna os horários disponiveis em um determinado dia para uma certa model.
     *
     * @param  \Carbon\Carbon $today Data para o qual ele irá fazer a busca.
     * @param  int $durationMinutes Serve para facilitar na hora de buscar horários livres que precisem ter uma certa duração.
     * @param  \Carbon\Carbon|null $openingTime Serve como referencia para buscar horários livres. Se for nulo, ele busca a referencia da config.
     * @return array
     */
    public function availableOn(Carbon $today, int $durationMinutes, Carbon $openingTime = null)
    {
    	return Scheduler::byModel(self::class)->availableOn($today, $durationMinutes, $openingTime);
    }
}