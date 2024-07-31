<?php

namespace Aleedhillon\MetaTraderClient;

use Illuminate\Support\ServiceProvider;

class MetaTraderClientServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'aleedhillon');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'aleedhillon');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/meta-trader-client.php', 'meta-trader-client');

        // Register the service the package provides.
        $this->app->singleton('meta-trader-client', function ($app) {
            return new MetaTraderClient;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['meta-trader-client'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/../config/meta-trader-client.php' => config_path('meta-trader-client.php'),
        ], 'meta-trader-client.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/aleedhillon'),
        ], 'meta-trader-client.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/aleedhillon'),
        ], 'meta-trader-client.assets');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/aleedhillon'),
        ], 'meta-trader-client.lang');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
