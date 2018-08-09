<?php namespace H4ad\Scheduler\Models;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

use Illuminate\Database\Eloquent\Model;

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
}