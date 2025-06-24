<?php

namespace Workbench\App\Console;

use Aleedhillon\MetaTraderClient\MetaTraderClient;
use Aleedhillon\MetaTraderClient\Exceptions\MetaTraderException;
use Illuminate\Console\Command;

class TestMetaTrader extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:mt5 {--demo : Use demo credentials}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test MetaTrader 5 connection and basic operations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 MetaTrader 5 Client Test');

        // Get connection details from environment or use demo values
        if ($this->option('demo')) {
            $this->info('📋 Using demo configuration...');
            $ip = "demo.mt5server.com";  // Replace with actual demo server
            $port = 443;
            $login = "demo_account";
            $password = "demo_password";
        } else {
            $ip = env('MT5_SERVER_IP');
            $port = env('MT5_SERVER_PORT', 443);
            $login = env('MT5_SERVER_WEB_LOGIN');
            $password = env('MT5_SERVER_WEB_PASSWORD');

            if (!$ip || !$login || !$password) {
                $this->error('❌ Missing MT5 connection details!');
                $this->info('Please set the following environment variables:');
                $this->info('- MT5_SERVER_IP');
                $this->info('- MT5_SERVER_PORT (optional, defaults to 443)');
                $this->info('- MT5_SERVER_WEB_LOGIN');
                $this->info('- MT5_SERVER_WEB_PASSWORD');
                $this->newLine();
                $this->info('Or use: php artisan test:mt5 --demo');
                return self::FAILURE;
            }
        }

        $timeout = 30;

        $this->info("📡 Connecting to {$ip}:{$port}");

        try {
            // Create MetaTrader client
            $client = new MetaTraderClient(
                ip: $ip,
                port: $port,
                login: $login,
                password: $password,
                timeout: $timeout,
            );

            // Test connection
            $this->info('🔗 Establishing connection...');
            $client->connect();
            $this->info('✅ Connected successfully!');

            // Test server time
            $this->info('⏰ Getting server time...');
            $time = $client->timeGet();
            $this->info('📅 Server Time: ' . date('Y-m-d H:i:s', $time->TimeServer));

            // Test server information
            $this->info('ℹ️ Getting server information...');
            $common = $client->commonGet();
            $this->info("📊 Server: {$common->Name}");
            $this->info("🏢 Owner: {$common->Owner}");
            $this->info("👥 Total Users: " . number_format($common->TotalUsers));
            $this->info("💼 Real Accounts: " . number_format($common->TotalUsersReal));
            $this->info("📈 Total Deals: " . number_format($common->TotalDeals));

            // Clean disconnect
            $this->info('🔌 Disconnecting...');
            $client->disconnect();
            $this->info('✅ Disconnected successfully!');
        } catch (MetaTraderException $e) {
            $this->error("❌ MetaTrader Error: {$e->getMessage()}");
            $this->error("Code: {$e->getMtCode()}");
            return self::FAILURE;
        } catch (\Exception $e) {
            $this->error("❌ Error: {$e->getMessage()}");
            return self::FAILURE;
        }

        $this->info('🎉 Test completed successfully!');
        return self::SUCCESS;
    }
}
