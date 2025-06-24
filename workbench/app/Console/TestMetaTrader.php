<?php

namespace Workbench\App\Console;

use Aleedhillon\MetaTraderClient\MetaTraderClient;
use Illuminate\Console\Command;

class TestMetaTrader extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:mt5';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 
    }
}
