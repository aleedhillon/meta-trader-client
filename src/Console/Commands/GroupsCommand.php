<?php

namespace Aleedhillon\MetaTraderClient\Console\Commands;

use Illuminate\Console\Command;
use Aleedhillon\MetaTraderClient\Facades\MetaTraderClient;
use Aleedhillon\MetaTraderClient\Exceptions\MetaTraderException;

class GroupsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mt5:groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display all MetaTrader 5 trading groups';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->displayHeader();

        if (!$this->checkConfiguration()) {
            return self::FAILURE;
        }

        $this->displayAllGroups();

        $this->displayFooter();

        return self::SUCCESS;
    }

    /**
     * Display the command header.
     */
    protected function displayHeader(): void
    {
        $this->info('👥 MetaTrader 5 Trading Groups');
        $this->info('═══════════════════════════════');
        $this->newLine();
    }

    /**
     * Check if configuration is complete.
     */
    protected function checkConfiguration(): bool
    {
        $config = config('meta-trader-client');
        $required = ['ip', 'login', 'password'];
        $missing = array_filter($required, fn($key) => empty($config[$key]));

        if (!empty($missing)) {
            $this->error('❌ Missing required configuration: ' . implode(', ', $missing));
            $this->info('💡 Run: php artisan vendor:publish --tag=meta-trader-client-config');
            return false;
        }

        return true;
    }

    /**
     * Display all groups.
     */
    protected function displayAllGroups(): void
    {
        if (!$this->ensureConnection()) {
            $this->error("❌ Connection failed, cannot retrieve groups");
            return;
        }

        try {
            $totalGroups = $this->executeWithRetry(function () {
                return MetaTraderClient::groupTotal();
            });

            if ($totalGroups === null || $totalGroups === 0) {
                $this->warn('No groups found');
                return;
            }

            $this->info("Found {$totalGroups} groups. Loading...");
            $this->newLine();

            $groups = [];
            $bar = $this->output->createProgressBar($totalGroups);
            $bar->start();

            for ($i = 0; $i < $totalGroups; $i++) {
                $group = $this->executeWithRetry(function () use ($i) {
                    return MetaTraderClient::groupNext($i);
                });

                if ($group) {
                    $groups[] = [
                        $group->Group ?? 'N/A',
                        $group->Company ?? 'N/A',
                        $group->Currency ?? 'USD',
                        number_format($group->Leverage ?? 0),
                        'N/A', // User count would require additional API calls
                    ];
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

            if (!empty($groups)) {
                $this->table(
                    ['Group Name', 'Company', 'Currency', 'Leverage', 'Users'],
                    $groups
                );
                $this->newLine();
                $this->info("✅ Successfully displayed {$totalGroups} groups");
            } else {
                $this->warn('No group information could be retrieved');
            }
        } catch (MetaTraderException $e) {
            $this->error("❌ Failed to get groups: {$e->getMessage()}");
        } catch (\Exception $e) {
            $this->error("❌ Failed to get groups: {$e->getMessage()}");
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
     * Display command footer.
     */
    protected function displayFooter(): void
    {
        $this->info('═══════════════════════════════');
        $this->info('📋 Groups report completed at ' . date('Y-m-d H:i:s T'));
        $this->newLine();
        $this->comment('💡 Use mt5:status for server details and mt5:symbols for symbol information');
    }
}
