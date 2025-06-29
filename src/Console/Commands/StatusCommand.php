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
    protected $signature = 'mt5:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display comprehensive MetaTrader 5 server status, connection information, and statistics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->displayHeader();

        $this->displayConfiguration();
        $this->newLine();

        $connectionData = $this->testConnection();

        if ($connectionData['connected']) {
            $this->displayServerInformation();
            $this->newLine();

            $this->displayServerStatistics();
            $this->newLine();

            $this->displayLicenseInformation();
            $this->newLine();

            $this->displayVersionInformation();
            $this->newLine();

            $this->displayGroupSample();
            $this->newLine();

            $this->displaySymbolSample();
        }

        $this->displayFooter();

        return self::SUCCESS;
    }

    /**
     * Display the command header.
     */
    protected function displayHeader(): void
    {
        $this->info('🚀 MetaTrader 5 Comprehensive Status Report');
        $this->info('═══════════════════════════════════════════');
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
                ['Login', $config['login'] ? '***' . substr($config['login'], -3) : 'Not set', 'MT5_SERVER_WEB_LOGIN'],
                ['Password', $config['password'] ? '***********' : 'Not set', 'MT5_SERVER_WEB_PASSWORD'],
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
     * Test connection to MT5 server.
     */
    protected function testConnection(): array
    {
        $this->info('🔍 Connection Status');
        $this->line('───────────────────');

        try {
            $config = config('meta-trader-client');

            if (empty($config['ip']) || empty($config['login']) || empty($config['password'])) {
                $this->error('❌ Cannot test connection: Missing required configuration');
                return ['connected' => false, 'error' => 'Missing configuration'];
            }

            // Show connection attempt details
            $this->comment("🔗 Attempting connection to {$config['ip']}:{$config['port']}...");
            $this->comment("🔧 Timeout setting: {$config['timeout']}s");

            // First, test basic network connectivity
            $this->comment('🌐 Testing network connectivity...');
            if (!$this->testNetworkConnectivity($config['ip'], $config['port'], $config['timeout'])) {
                $this->error("❌ Cannot reach server at {$config['ip']}:{$config['port']}");
                $this->comment("💡 Check if the server is running and accessible from your network");
                return ['connected' => false, 'error' => 'Network connectivity failed'];
            }
            $this->comment('✓ Network connectivity confirmed');

            $startTime = microtime(true);

            // Test basic connection with progress feedback
            $this->comment('⏳ Testing server connectivity...');

            // Add timeout handling using set_time_limit
            $originalTimeLimit = ini_get('max_execution_time');
            set_time_limit(10); // 60 seconds max for this operation

            try {
                $connectionStart = microtime(true);
                $this->comment('   → Calling timeGet()...');

                $time = MetaTraderClient::timeGet();
                $connectionTime = round((microtime(true) - $connectionStart) * 1000, 2);

                $this->comment("✓ Server time retrieved ({$connectionTime}ms)");
            } catch (MetaTraderException $e) {
                set_time_limit($originalTimeLimit); // Restore original limit
                $this->error("❌ Failed to get server time: {$e->getMessage()}");
                $this->error("Error Code: {$e->getMtCode()}");
                return ['connected' => false, 'error' => $e->getMessage()];
            } catch (\Exception $e) {
                set_time_limit($originalTimeLimit); // Restore original limit
                $this->error("❌ Connection timeout or error: {$e->getMessage()}");
                return ['connected' => false, 'error' => $e->getMessage()];
            }

            try {
                $this->comment('⏳ Getting server timestamp...');
                $timestampStart = microtime(true);
                $this->comment('   → Calling timeServer()...');

                $serverTime = MetaTraderClient::timeServer();
                $timestampTime = round((microtime(true) - $timestampStart) * 1000, 2);

                $this->comment("✓ Server timestamp retrieved ({$timestampTime}ms)");
            } catch (MetaTraderException $e) {
                $this->warn("⚠️ Failed to get server timestamp, using time info instead");
                $serverTime = is_string($time->TimeServer) ? strtotime($time->TimeServer) : $time->TimeServer;
            } catch (\Exception $e) {
                $this->warn("⚠️ Timestamp error, using time info instead: {$e->getMessage()}");
                $serverTime = is_string($time->TimeServer) ? strtotime($time->TimeServer) : $time->TimeServer;
            }

            // Restore original time limit
            set_time_limit($originalTimeLimit);

            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000, 2);

            $this->info("✅ Connection successful! ({$duration}ms)");
            $this->info('📅 Server Time: ' . date('Y-m-d H:i:s T', $serverTime));

            // Test ping
            $this->comment('⏳ Testing ping...');
            try {
                MetaTraderClient::ping();
                $this->info('🏓 Ping: Successful');
            } catch (MetaTraderException $e) {
                $this->warn('🏓 Ping: Failed - ' . $e->getMessage());
            }

            return ['connected' => true, 'duration' => $duration, 'server_time' => $serverTime];
        } catch (MetaTraderException $e) {
            $this->error("❌ MT5 Error: {$e->getMessage()}");
            $this->error("Error Code: {$e->getMtCode()}");
            $this->comment("💡 Check your server IP, login credentials, and network connectivity");
            return ['connected' => false, 'error' => $e->getMessage()];
        } catch (\Exception $e) {
            $this->error("❌ Connection Error: {$e->getMessage()}");
            $this->comment("💡 This might be a network timeout or server unavailability issue");
            return ['connected' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Display server information.
     */
    protected function displayServerInformation(): void
    {
        $this->info('🖥️  Server Information');
        $this->line('─────────────────────');

        try {
            $common = MetaTraderClient::commonGet();

            $this->table(
                ['Property', 'Value'],
                [
                    ['Server Name', $common->Name ?? 'N/A'],
                    ['Owner', $common->Owner ?? 'N/A'],
                    ['Owner ID', $common->OwnerID ?? 'N/A'],
                    ['Owner Host', $common->OwnerHost ?? 'N/A'],
                    ['Owner Email', $common->OwnerEmail ?? 'N/A'],
                    ['Product', $common->Product ?? 'N/A'],
                    ['Account URL', $common->AccountURL ?? 'N/A'],
                    ['Account Auto', $common->AccountAuto ? 'Yes' : 'No'],
                    ['Live Update Mode', $common->LiveUpdateMode ?? 0],
                ]
            );
        } catch (MetaTraderException $e) {
            $this->error("❌ Failed to get server information: {$e->getMessage()}");
        }
    }

    /**
     * Display server statistics.
     */
    protected function displayServerStatistics(): void
    {
        $this->info('📊 Server Statistics');
        $this->line('────────────────────');

        try {
            $common = MetaTraderClient::commonGet();

            // Get additional counts
            $totalGroups = 0;
            $totalSymbols = 0;

            try {
                $totalGroups = MetaTraderClient::groupTotal();
            } catch (MetaTraderException $e) {
                // Ignore error, use 0
            }

            try {
                $totalSymbols = MetaTraderClient::symbolTotal();
            } catch (MetaTraderException $e) {
                // Ignore error, use 0
            }

            $this->table(
                ['Metric', 'Count'],
                [
                    ['Total Users', number_format($common->TotalUsers ?? 0)],
                    ['Real Users', number_format($common->TotalUsersReal ?? 0)],
                    ['Total Deals', number_format($common->TotalDeals ?? 0)],
                    ['Total Orders', number_format($common->TotalOrders ?? 0)],
                    ['History Orders', number_format($common->TotalOrdersHistory ?? 0)],
                    ['Total Positions', number_format($common->TotalPositions ?? 0)],
                    ['Total Groups', number_format($totalGroups)],
                    ['Total Symbols', number_format($totalSymbols)],
                ]
            );
        } catch (MetaTraderException $e) {
            $this->error("❌ Failed to get server statistics: {$e->getMessage()}");
        }
    }

    /**
     * Display license information.
     */
    protected function displayLicenseInformation(): void
    {
        $this->info('📝 License Information');
        $this->line('─────────────────────');

        try {
            $common = MetaTraderClient::commonGet();

            $licenseExpiry = $common->ExpirationLicense ?? 0;
            $supportExpiry = $common->ExpirationSupport ?? 0;

            $this->table(
                ['License Property', 'Value'],
                [
                    ['License Expiration', $licenseExpiry ? date('Y-m-d H:i:s', $licenseExpiry) : 'N/A'],
                    ['Support Expiration', $supportExpiry ? date('Y-m-d H:i:s', $supportExpiry) : 'N/A'],
                    ['Trade Servers Limit', number_format($common->LimitTradeServers ?? 0)],
                    ['Web Servers Limit', number_format($common->LimitWebServers ?? 0)],
                    ['Accounts Limit', number_format($common->LimitAccounts ?? 0)],
                    ['Deals Limit', number_format($common->LimitDeals ?? 0)],
                    ['Symbols Limit', number_format($common->LimitSymbols ?? 0)],
                    ['Groups Limit', number_format($common->LimitGroups ?? 0)],
                ]
            );

            // Check if license is about to expire (within 30 days)
            if ($licenseExpiry && $licenseExpiry < strtotime('+30 days')) {
                $this->warn('⚠️  License expires within 30 days!');
            }
        } catch (MetaTraderException $e) {
            $this->error("❌ Failed to get license information: {$e->getMessage()}");
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
     * Display sample of groups.
     */
    protected function displayGroupSample(): void
    {
        $this->info('👥 Groups Sample (First 5)');
        $this->line('─────────────────────────');

        try {
            $totalGroups = MetaTraderClient::groupTotal();

            if ($totalGroups === 0) {
                $this->warn('No groups found');
                return;
            }

            $groups = [];
            $maxGroups = min($totalGroups, 5);

            for ($i = 0; $i < $maxGroups; $i++) {
                try {
                    $group = MetaTraderClient::groupNext($i);

                    // Try to get user count for this group
                    $userCount = 0;
                    try {
                        $userLogins = MetaTraderClient::userLogins($group->Group);
                        $userCount = count($userLogins);
                    } catch (MetaTraderException $e) {
                        // Ignore error, use 0
                    }

                    $groups[] = [
                        $group->Group ?? 'N/A',
                        $group->Company ?? 'N/A',
                        $group->Currency ?? 'USD',
                        number_format($group->Leverage ?? 0),
                        number_format($userCount),
                    ];
                } catch (MetaTraderException $e) {
                    continue;
                }
            }

            if (!empty($groups)) {
                $this->table(
                    ['Group Name', 'Company', 'Currency', 'Leverage', 'Users'],
                    $groups
                );
            } else {
                $this->warn('No group information could be retrieved');
            }
        } catch (MetaTraderException $e) {
            $this->error("❌ Failed to get groups: {$e->getMessage()}");
        }
    }

    /**
     * Display sample of symbols.
     */
    protected function displaySymbolSample(): void
    {
        $this->info('📈 Symbols Sample (First 5)');
        $this->line('──────────────────────────');

        try {
            $totalSymbols = MetaTraderClient::symbolTotal();

            if ($totalSymbols === 0) {
                $this->warn('No symbols found');
                return;
            }

            $symbols = [];
            $maxSymbols = min($totalSymbols, 5);

            for ($i = 0; $i < $maxSymbols; $i++) {
                try {
                    $symbol = MetaTraderClient::symbolNext($i);

                    $symbols[] = [
                        $symbol->Symbol ?? 'N/A',
                        $symbol->Description ?? 'N/A',
                        $symbol->CurrencyBase ?? 'N/A',
                        $symbol->CurrencyProfit ?? 'N/A',
                        $symbol->Path ?? 'N/A',
                    ];
                } catch (MetaTraderException $e) {
                    continue;
                }
            }

            if (!empty($symbols)) {
                $this->table(
                    ['Symbol', 'Description', 'Base Currency', 'Profit Currency', 'Path'],
                    $symbols
                );
            } else {
                $this->warn('No symbol information could be retrieved');
            }
        } catch (MetaTraderException $e) {
            $this->error("❌ Failed to get symbols: {$e->getMessage()}");
        }
    }

    /**
     * Test basic network connectivity to the server.
     */
    protected function testNetworkConnectivity(string $host, int $port, int $timeout): bool
    {
        try {
            $context = stream_context_create([
                'socket' => [
                    'timeout' => $timeout,
                ],
            ]);

            $connection = @stream_socket_client(
                "tcp://{$host}:{$port}",
                $errno,
                $errstr,
                $timeout,
                STREAM_CLIENT_CONNECT,
                $context
            );

            if ($connection === false) {
                $this->comment("   → Socket error: {$errstr} (Code: {$errno})");
                return false;
            }

            fclose($connection);
            return true;
        } catch (\Exception $e) {
            $this->comment("   → Network test failed: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Display command footer.
     */
    protected function displayFooter(): void
    {
        $this->info('═══════════════════════════════════════════');
        $this->info('📋 Status report completed at ' . date('Y-m-d H:i:s T'));
        $this->newLine();
        $this->comment('💡 Tip: Configure missing settings with: php artisan vendor:publish --tag=meta-trader-client-config');
    }
}
