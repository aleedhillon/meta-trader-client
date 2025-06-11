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
            $config = $app['config']['meta-trader-client'];

            return new MetaTraderClient(
                $config['agent'] ?? 'WebAPI',
                $config['should_crypt'] ?? true,
                $config['ip'] ?? null,
                $config['port'] ?? null,
                $config['timeout'] ?? null,
                $config['login'] ?? null,
                $config['password'] ?? null
            );
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
    }
}
