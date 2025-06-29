<?php

namespace Aleedhillon\MetaTraderClient\Console\Commands;

use Illuminate\Console\Command;
use Aleedhillon\MetaTraderClient\Facades\MetaTraderClient;
use Aleedhillon\MetaTraderClient\Exceptions\MetaTraderException;

class SymbolsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mt5:symbols';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display all MetaTrader 5 trading symbols';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->displayHeader();

        if (!$this->checkConfiguration()) {
            return self::FAILURE;
        }

        $this->displayAllSymbols();

        $this->displayFooter();

        return self::SUCCESS;
    }

    /**
     * Display the command header.
     */
    protected function displayHeader(): void
    {
        $this->info('📈 MetaTrader 5 Trading Symbols');
        $this->info('════════════════════════════════');
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
     * Display all symbols.
     */
    protected function displayAllSymbols(): void
    {
        if (!$this->ensureConnection()) {
            $this->error("❌ Connection failed, cannot retrieve symbols");
            return;
        }

        try {
            $totalSymbols = $this->executeWithRetry(function () {
                return MetaTraderClient::symbolTotal();
            });

            if ($totalSymbols === null || $totalSymbols === 0) {
                $this->warn('No symbols found');
                return;
            }

            $this->info("Found {$totalSymbols} symbols. Loading...");
            $this->newLine();

            $symbols = [];
            $bar = $this->output->createProgressBar($totalSymbols);
            $bar->start();

            for ($i = 0; $i < $totalSymbols; $i++) {
                $symbol = $this->executeWithRetry(function () use ($i) {
                    return MetaTraderClient::symbolNext($i);
                });

                if ($symbol) {
                    $symbols[] = [
                        $symbol->Symbol ?? 'N/A',
                        $symbol->Description ?? 'N/A',
                        $symbol->CurrencyBase ?? 'N/A',
                        $symbol->CurrencyProfit ?? 'N/A',
                        $symbol->Digits ?? 'N/A',
                        $symbol->Path ?? 'N/A',
                    ];
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

            if (!empty($symbols)) {
                $this->table(
                    ['Symbol', 'Description', 'Base Currency', 'Profit Currency', 'Digits', 'Path'],
                    $symbols
                );
                $this->newLine();
                $this->info("✅ Successfully displayed {$totalSymbols} symbols");
            } else {
                $this->warn('No symbol information could be retrieved');
            }
        } catch (MetaTraderException $e) {
            $this->error("❌ Failed to get symbols: {$e->getMessage()}");
        } catch (\Exception $e) {
            $this->error("❌ Failed to get symbols: {$e->getMessage()}");
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
        $this->info('════════════════════════════════');
        $this->info('📋 Symbols report completed at ' . date('Y-m-d H:i:s T'));
        $this->newLine();
        $this->comment('💡 Use mt5:status for server details and mt5:groups for group information');
    }
}
