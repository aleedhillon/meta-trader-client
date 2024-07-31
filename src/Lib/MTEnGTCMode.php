<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * orders expiration modes
 */
class MTEnGTCMode
{
    const ORDERS_GTC = 0;
    const ORDERS_DAILY = 1;
    const ORDERS_DAILY_NO_STOPS = 2;
    //--- enumeration borders
    const ORDERS_FIRST = MTEnGTCMode::ORDERS_GTC;
    const ORDERS_LAST = MTEnGTCMode::ORDERS_DAILY_NO_STOPS;
}
