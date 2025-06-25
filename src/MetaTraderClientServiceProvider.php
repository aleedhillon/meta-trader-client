<?php

namespace Aleedhillon\MetaTraderClient;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Console\AboutCommand;

class MetaTraderClientServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPublishing();
        $this->registerCommands();
        $this->registerAboutCommand();
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
     * Register package publishing options.
     *
     * @return void
     */
    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            // Publishing the configuration file with package-specific tag
            $this->publishes([
                __DIR__ . '/../config/meta-trader-client.php' => config_path('meta-trader-client.php'),
            ], 'meta-trader-client-config');
        }
    }

    /**
     * Register package commands.
     *
     * @return void
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            // Register package commands
            $this->commands([
                Console\Commands\StatusCommand::class,
            ]);

            // Register workbench commands if they exist
            if (class_exists(\Workbench\App\Console\TestMetaTrader::class)) {
                $this->commands([
                    \Workbench\App\Console\TestMetaTrader::class,
                ]);
            }
        }
    }

    /**
     * Register information in the "About" artisan command.
     *
     * @return void
     */
    protected function registerAboutCommand(): void
    {
        if (class_exists(AboutCommand::class)) {
            AboutCommand::add('MetaTrader Client', fn() => [
                'Version' => $this->getPackageVersion(),
                'WebAPI Version' => defined('WebAPIVersion') ? WebAPIVersion : 'Unknown',
                'WebAPI Date' => defined('WebAPIDate') ? WebAPIDate : 'Unknown',
                'PHP Version' => PHP_VERSION,
                'Laravel Support' => '^12.18',
            ]);
        }
    }

    /**
     * Get the package version from composer.json.
     *
     * @return string
     */
    protected function getPackageVersion(): string
    {
        $composerFile = __DIR__ . '/../composer.json';

        if (file_exists($composerFile)) {
            $composer = json_decode(file_get_contents($composerFile), true);
            return $composer['version'] ?? 'dev-main';
        }

        return 'unknown';
    }
}
