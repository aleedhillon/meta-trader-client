<?php

namespace Aleedhillon\MetaTraderClient\Console\Commands;

use Illuminate\Console\Command;
use Aleedhillon\MetaTraderClient\Facades\MetaTraderClient;
use Aleedhillon\MetaTraderClient\Exceptions\MetaTraderException;

class StatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mt5:status 
                            {--config : Show current configuration}
                            {--test : Test connection to MT5 server}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show MetaTrader 5 client status and configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 MetaTrader 5 Client Status');
        $this->newLine();

        if ($this->option('config')) {
            $this->showConfiguration();
        }

        if ($this->option('test')) {
            $this->testConnection();
        }

        if (!$this->option('config') && !$this->option('test')) {
            $this->showConfiguration();
            $this->newLine();
            $this->testConnection();
        }

        return self::SUCCESS;
    }

    /**
     * Show current configuration.
     */
    protected function showConfiguration(): void
    {
        $this->info('📋 Current Configuration:');

        $config = config('meta-trader-client');

        $this->table(
            ['Setting', 'Value', 'Environment Variable'],
            [
                ['Agent', $config['agent'] ?? 'Not set', 'MT5_AGENT'],
                ['Should Encrypt', $config['should_crypt'] ? 'Yes' : 'No', 'MT5_SHOULD_CRYPT'],
                ['Server IP', $config['ip'] ?? 'Not set', 'MT5_SERVER_IP'],
                ['Server Port', $config['port'] ?? 'Not set', 'MT5_SERVER_PORT'],
                ['Login', $config['login'] ? '***' : 'Not set', 'MT5_SERVER_WEB_LOGIN'],
                ['Password', $config['password'] ? '***' : 'Not set', 'MT5_SERVER_WEB_PASSWORD'],
                ['Timeout', $config['timeout'] ?? 'Not set', 'MT5_SERVER_TIMEOUT'],
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
     * Test connection to MT5 server.
     */
    protected function testConnection(): void
    {
        $this->info('🔍 Testing MT5 Connection...');

        try {
            $config = config('meta-trader-client');

            if (empty($config['ip']) || empty($config['login']) || empty($config['password'])) {
                $this->error('❌ Cannot test connection: Missing required configuration');
                return;
            }

            $startTime = microtime(true);

            // Test basic connection
            $time = MetaTraderClient::timeGet();
            $endTime = microtime(true);

            $duration = round(($endTime - $startTime) * 1000, 2);

            $this->info("✅ Connection successful! ({$duration}ms)");

            // Handle TimeServer property - it might be string or int
            $serverTime = $time->TimeServer;
            if (is_string($serverTime)) {
                $this->info('📅 Server Time: ' . $serverTime);
            } else {
                $this->info('📅 Server Time: ' . date('Y-m-d H:i:s', $serverTime));
            }

            // Get server info
            $common = MetaTraderClient::commonGet();
            $this->info("📊 Server: {$common->Name}");
            $this->info("🏢 Owner: {$common->Owner}");
            $this->info("👥 Total Users: " . number_format($common->TotalUsers));
        } catch (MetaTraderException $e) {
            $this->error("❌ MT5 Error: {$e->getMessage()}");
            $this->error("Error Code: {$e->getMtCode()}");
        } catch (\Exception $e) {
            $this->error("❌ Connection Error: {$e->getMessage()}");
        }
    }
}
