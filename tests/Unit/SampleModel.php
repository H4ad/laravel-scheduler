<?php namespace H4ad\Scheduler\Tests\Unit;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

use Illuminate\Database\Eloquent\Model;
use H4ad\Scheduler\Traits\SchedulerModelTrait;

/**
 * Classe de exemplo que possui implementação do SchedulerModelTrait.
 */
class SampleModel extends Model
{
	use SchedulerModelTrait;

	/**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $guarded = [];
    protected $table = 'sample_models';
}