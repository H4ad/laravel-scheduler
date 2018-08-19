<?php namespace H4ad\Scheduler\Facades;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool hasScheduleBetween($model_type, $start_at, $end_at)
 * @method static array availableToday($model_type, $duration, $openingTime = null)
 * @method static array availableOn($model_type, $today, $durationMinutes, $openingTime = null)
 * @method static array validateSchedule($start_at, $end_at = null, $status = null)
 * @method static \Carbon\Carbon parseToCarbon($date, $reference = null)
 * @method static \H4ad\Scheduler\Models\Schedule|null parseToSchedule($value)
 *
 * @see \H4ad\Scheduler\Scheduler
 */
class Scheduler extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'scheduler';
    }
}