<?php

namespace Aleedhillon\MetaTraderClient\Console\Commands;

use Illuminate\Console\Command;
use Aleedhillon\MetaTraderClient\Facades\MetaTraderClient;

class InfoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mt5';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display MetaTrader 5 package configuration and basic information';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->displayHeader();

        $this->displayConfiguration();
        $this->newLine();

        $this->displayVersionInformation();
        $this->newLine();

        $this->displayAvailableCommands();

        $this->displayFooter();

        return self::SUCCESS;
    }

    /**
     * Display the command header.
     */
    protected function displayHeader(): void
    {
        $this->info('🚀 MetaTrader 5 Package Information');
        $this->info('══════════════════════════════════');
        $this->newLine();
    }

    /**
     * Display current configuration.
     */
    protected function displayConfiguration(): void
    {
        $this->info('📋 Configuration Settings');
        $this->line('──────────────────────────');

        $config = config('meta-trader-client');

        $this->table(
            ['Setting', 'Value', 'Environment Variable'],
            [
                ['Agent', $config['agent'] ?? 'Not set', 'MT5_AGENT'],
                ['Encryption', $config['should_crypt'] ? 'Enabled' : 'Disabled', 'MT5_SHOULD_CRYPT'],
                ['Server IP', $config['ip'] ?? 'Not set', 'MT5_SERVER_IP'],
                ['Server Port', $config['port'] ?? 'Not set', 'MT5_SERVER_PORT'],
                ['Login', $config['login'] ?? 'Not set', 'MT5_SERVER_WEB_LOGIN'],
                ['Password', $config['password'] ?? 'Not set', 'MT5_SERVER_WEB_PASSWORD'],
                ['Timeout', ($config['timeout'] ?? 'Not set') . 's', 'MT5_SERVER_TIMEOUT'],
            ]
        );

        // Check if configuration is complete
        $required = ['ip', 'login', 'password'];
        $missing = array_filter($required, fn($key) => empty($config[$key]));

        if (!empty($missing)) {
            $this->warn('⚠️  Missing required configuration: ' . implode(', ', $missing));
            $this->info('💡 Run: php artisan vendor:publish --tag=meta-trader-client-config');
        } else {
            $this->info('✅ Configuration is complete');
        }
    }

    /**
     * Display version information.
     */
    protected function displayVersionInformation(): void
    {
        $this->info('🔢 Version Information');
        $this->line('─────────────────────');

        try {
            $versionInfo = MetaTraderClient::getVersionInfo();

            $this->table(
                ['Component', 'Version'],
                [
                    ['Package Version', $versionInfo['package_version'] ?? 'N/A'],
                    ['Web API Version', $versionInfo['web_api_version'] ?? 'N/A'],
                    ['Build', $versionInfo['build'] ?? 'N/A'],
                    ['API Version', $versionInfo['api_version'] ?? 'N/A'],
                    ['PHP Version', PHP_VERSION],
                    ['Laravel Version', app()->version()],
                ]
            );
        } catch (\Exception $e) {
            $this->error("❌ Failed to get version information: {$e->getMessage()}");
        }
    }

    /**
     * Display available commands.
     */
    protected function displayAvailableCommands(): void
    {
        $this->info('🎯 Available Commands');
        $this->line('────────────────────');

        $this->table(
            ['Command', 'Description'],
            [
                ['mt5', 'Show package configuration and basic info (this command)'],
                ['mt5:status', 'Show server status, connection info, and statistics'],
                ['mt5:groups', 'Show all trading groups with progress indicator'],
                ['mt5:symbols', 'Show all trading symbols with progress indicator'],
            ]
        );
    }

    /**
     * Display command footer.
     */
    protected function displayFooter(): void
    {
        $this->info('══════════════════════════════════');
        $this->info('📋 Package info displayed at ' . date('Y-m-d H:i:s T'));
        $this->newLine();
        $this->comment('💡 Use mt5:status to test your connection and view server details');
        $this->comment('💡 Configure settings with: php artisan vendor:publish --tag=meta-trader-client-config');
    }
}
