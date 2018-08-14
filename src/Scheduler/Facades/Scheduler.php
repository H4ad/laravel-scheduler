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
 * @method static bool hasScheduleBetween($start_at, $end_at)
 * @method static array availableToday($model_type, $duration, $openingTime = null)
 * @method static array availableOn($model_type, $today, $durationMinutes, $openingTime = null)
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