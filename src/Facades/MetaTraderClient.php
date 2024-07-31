<?php

namespace Aleedhillon\MetaTraderClient\Facades;

use Illuminate\Support\Facades\Facade;

class MetaTraderClient extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'meta-trader-client';
    }
}
