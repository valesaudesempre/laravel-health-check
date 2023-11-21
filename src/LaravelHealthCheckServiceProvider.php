<?php

namespace ValeSaude\LaravelHealthCheck;

use Illuminate\Support\ServiceProvider;

/**
 * @codeCoverageIgnore
 */
class LaravelHealthCheckServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-health-check.php', 'laravel-health-check');

        $this->commands([
            Commands\RunCommand::class,
        ]);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/laravel-health-check.php' => config_path('laravel-health-check.php'),
        ], 'laravel-health-check-config');
    }
}