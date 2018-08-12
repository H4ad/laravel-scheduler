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
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('scheduler.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__.'/Migrations');
        $this->loadTranslationsFrom(__DIR__.'/Translations', 'scheduler');

        $this->publishes([
            __DIR__.'/Translations' => resource_path('lang/vendor/scheduler'),
        ]);
    }

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

        $this->mergeConfig();
    }

    /**
     * Mescla configurações do usuário com as configurações do Scheduler.
     *
     * @return void
     */
    private function mergeConfig()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php', 'scheduler'
        );
    }
}