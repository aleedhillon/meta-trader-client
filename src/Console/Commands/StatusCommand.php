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
    protected $description = 'Display MetaTrader 5 server status, connection information, and statistics';

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
        }

        $this->displayFooter();

        return self::SUCCESS;
    }

    /**
     * Display the command header.
     */
    protected function displayHeader(): void
    {
        $this->info('🚀 MetaTrader 5 Server Status Report');
        $this->info('═══════════════════════════════════════');
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

            // Test basic network connectivity
            if (!$this->testNetworkConnectivity($config['ip'], $config['port'], $config['timeout'])) {
                $this->error("❌ Cannot reach server at {$config['ip']}:{$config['port']}");
                return ['connected' => false, 'error' => 'Network connectivity failed'];
            }

            $startTime = microtime(true);

            // Set timeout for operations
            $originalTimeLimit = ini_get('max_execution_time');
            set_time_limit(30);

            try {
                $time = MetaTraderClient::timeGet();
                $serverTime = MetaTraderClient::timeServer();

                set_time_limit($originalTimeLimit);

                $duration = round((microtime(true) - $startTime) * 1000, 2);

                $this->info("✅ Connection successful! ({$duration}ms)");
                $this->info('📅 Server Time: ' . date('Y-m-d H:i:s T', $serverTime));

                // Test ping
                try {
                    MetaTraderClient::ping();
                    $this->info('🏓 Ping: Successful');
                } catch (MetaTraderException $e) {
                    $this->warn('🏓 Ping: Failed');
                }

                return ['connected' => true, 'duration' => $duration, 'server_time' => $serverTime];
            } catch (MetaTraderException $e) {
                set_time_limit($originalTimeLimit);
                $this->error("❌ Failed to connect: {$e->getMessage()}");
                return ['connected' => false, 'error' => $e->getMessage()];
            } catch (\Exception $e) {
                set_time_limit($originalTimeLimit);
                $this->error("❌ Connection error: {$e->getMessage()}");
                return ['connected' => false, 'error' => $e->getMessage()];
            }
        } catch (MetaTraderException $e) {
            $this->error("❌ MT5 Error: {$e->getMessage()}");
            return ['connected' => false, 'error' => $e->getMessage()];
        } catch (\Exception $e) {
            $this->error("❌ Connection Error: {$e->getMessage()}");
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

        if (!$this->ensureConnection()) {
            $this->error("❌ Connection lost, skipping server information");
            return;
        }

        try {
            $common = $this->executeWithRetry(function () {
                return MetaTraderClient::commonGet();
            });

            if ($common) {
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
            } else {
                $this->error("❌ Failed to get server information");
            }
        } catch (MetaTraderException $e) {
            $this->error("❌ Failed to get server information: {$e->getMessage()}");
        } catch (\Exception $e) {
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

        if (!$this->ensureConnection()) {
            $this->error("❌ Connection lost, skipping server statistics");
            return;
        }

        try {
            $common = $this->executeWithRetry(function () {
                return MetaTraderClient::commonGet();
            });

            if (!$common) {
                $this->error("❌ Failed to get server statistics");
                return;
            }

            // Get additional counts
            $totalGroups = 0;
            $totalSymbols = 0;

            $groupCount = $this->executeWithRetry(function () {
                return MetaTraderClient::groupTotal();
            });
            if ($groupCount !== null) {
                $totalGroups = $groupCount;
            }

            $symbolCount = $this->executeWithRetry(function () {
                return MetaTraderClient::symbolTotal();
            });
            if ($symbolCount !== null) {
                $totalSymbols = $symbolCount;
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
        } catch (\Exception $e) {
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

        if (!$this->ensureConnection()) {
            $this->error("❌ Connection lost, skipping license information");
            return;
        }

        try {
            $common = $this->executeWithRetry(function () {
                return MetaTraderClient::commonGet();
            });

            if (!$common) {
                $this->error("❌ Failed to get license information");
                return;
            }

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
        } catch (\Exception $e) {
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
     * Execute a function with retry logic for connection issues.
     */
    protected function executeWithRetry(callable $function, int $maxRetries = 2)
    {
        $attempt = 0;

        while ($attempt < $maxRetries) {
            try {
                if ($attempt > 0) {
                    // Force reconnection on retry
                    if (MetaTraderClient::isConnected()) {
                        MetaTraderClient::disconnect();
                        usleep(500000); // 0.5 second delay
                    }
                    MetaTraderClient::connect();
                    usleep(200000); // 0.2 second delay after connect
                }

                return $function();
            } catch (\Exception $e) {
                $attempt++;
                $errorMsg = $e->getMessage();

                // Check for socket-related errors that indicate connection issues
                if (
                    strpos($errorMsg, 'Broken pipe') !== false ||
                    strpos($errorMsg, 'socket_write') !== false ||
                    strpos($errorMsg, 'Network error') !== false ||
                    strpos($errorMsg, 'Connection lost') !== false
                ) {
                    if ($attempt < $maxRetries) {
                        continue;
                    }
                } else {
                    // For non-connection errors, don't retry
                    throw $e;
                }
            }
        }

        return null;
    }

    /**
     * Ensure MT5 connection is still active.
     */
    protected function ensureConnection(): bool
    {
        try {
            // Always disconnect and reconnect to ensure fresh connection
            if (MetaTraderClient::isConnected()) {
                MetaTraderClient::disconnect();
                usleep(100000); // 0.1 second delay after disconnect
            }

            MetaTraderClient::connect();

            // Test connection with a simple call
            try {
                MetaTraderClient::timeServer();
                return true;
            } catch (\Exception $e) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
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
                return false;
            }

            fclose($connection);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Display command footer.
     */
    protected function displayFooter(): void
    {
        $this->info('═══════════════════════════════════════');
        $this->info('📋 Status report completed at ' . date('Y-m-d H:i:s T'));
        $this->newLine();
        $this->comment('💡 Use mt5:groups and mt5:symbols to view groups and symbols data');
        $this->comment('💡 Configure missing settings with: php artisan vendor:publish --tag=meta-trader-client-config');
    }
}
