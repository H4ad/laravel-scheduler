<?php namespace H4ad\Scheduler\Models;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

use Illuminate\Database\Eloquent\Model;
use H4ad\Scheduler\Exceptions\ModelNotFound;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use SoftDeletes;

	/**
     * Os atributos que podem ser atribuíveis em massa.
     *
     * @var array
     */
    protected $fillable = [
    	'model_type', 'model_id', 'start_at', 'end_at', 'status'
    ];

    /**
     * Os atributos que devem ser transformados para data.
     *
     * @var array
     */
    protected $dates = [
    	'start_at', 'end_at', 'deleted_at'
    ];

    /**
     * Seta um status para o horário agendado.
     *
     * @param int|string $status Pode ser passado o ID do status ou seu nome para seta-lo no horário.
     */
    public function setStatus($name)
    {
    	$this->fill($this->parseStatusKey($name))->save();
    }

    /**
     * Retorna o ID do status caso passem o nome do status.
     *
     * @param  int|string $key ID ou o nome do status.
     * @return array
     */
    public function parseStatusKey($key)
    {
    	if(is_int($key))
    		return ['status' => $key];

    	$status = ScheduleStatus::where('name', $key)->first();

    	if(is_null($status))
    		throw (new ModelNotFound)->setValues(ScheduleStatus::class);

    	return ['status' => $status->id];
    }

    /**
     * Escopo de uma consulta que busca horarios pela data de início.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Carbon\Carbon|string $start_at
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStartAt($query, $start_at)
    {
        return $query->where('start_at', $start_at);
    }
}