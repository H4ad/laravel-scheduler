<?php namespace H4ad\Scheduler;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

use H4ad\Scheduler\Models\Schedule;

class Scheduler
{
    /**
     * Laravel application
     *
     * @var \Illuminate\Foundation\Application
     */
    public $app;

    /**
     * Create a new confide instance.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Escopo de uma consulta que busca horarios pela data de início.
     *
     * @param string|Carbon\Carbon $start_at
     * @param string|Carbon\Carbon $end_at
     * @return bool
     */
    public function hasScheduleBetween($start_at, $end_at)
    {
        return !is_null(
            Schedule::latest()
                ->where('start_at', '>=', $start_at)
                ->where('end_at', '<=', $end_at)
                ->first()
        );
    }
}