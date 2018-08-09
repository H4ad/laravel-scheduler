<?php namespace H4ad\Scheduler\Models;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;

class ScheduleStatus extends Model
{
    use SoftDeletes;

    /**
     * O nome da tabela que essa model representa.
     *
     * @var string
     */
    protected $table = Config::get('schedule_status_table');

	/**
     * Os atributos que podem ser atribuíveis em massa.
     *
     * @var array
     */
    protected $fillable = [
    	'name', 'description'
    ];

    /**
     * Os atributos que devem ser transformados para data.
     *
     * @var array
     */
    protected $dates = [
    	'deleted_at'
    ];
}