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
 * @method static void init(Closure $boot)
 * @method static $this avoid(array $model_ids)
 * @method static boolean hasConflict($start_at, $end_at)
 * @method static $this whereBetween($start_at, $end_at)
 * @method static $this byModel(string $model_type = null)
 * @method static array availableToday(int $duration, Carbon $openingTime = null)
 * @method static array availableOn(Carbon $today, int $durationMinutes, Carbon $openingTime = null)
 * @method static boolean isShouldntAdd(Carbon $opening, Carbon $closing, Carbon $start, Carbon $end)
 * @method static array validateSchedule($start_at, $end_at = null, int $status = null)
 * @method static \Carbon\Carbon parseToCarbon($date, $reference = null)
 * @method static \H4ad\Scheduler\Models\Schedule|null parseToSchedule($value)
 * @method static $this newInstance()
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
        return static::$app['scheduler']->newInstance();
    }
}