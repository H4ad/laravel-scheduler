<?php namespace H4ad\Scheduler;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

use Illuminate\Support\ServiceProvider;

class SchedulerServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Scheduler::class, function ($app) {
            return new Scheduler($app);
        });

        $this->app->alias(Scheduler::class, 'scheduler');
    }
}