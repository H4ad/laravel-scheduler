<?php namespace H4ad\Scheduler\Models;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleStatus extends Model
{
    use SoftDeletes;

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

    /**
     * Construtor para inicilizar a váriavel table.
     */
    public function __construct()
    {
        parent::__construct();

        $this->table = Config::get('scheduler.schedule_status_table');
    }
}