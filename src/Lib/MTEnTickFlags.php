<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * tick collection flags
 */
class MTEnTickFlags
{
    const TICK_REALTIME = 1; // allow realtime tick apply
    const TICK_COLLECTRAW = 2; // allow to collect raw ticks
    const TICK_FEED_STATS = 4; // allow to receive price statisticks from datafeeds
    const TICK_NEGATIVE_PRICES = 8; // allow to receive negative prices
    //--- flags borders
    const TICK_NONE = 0;
    const TICK_ALL = 15; // TICK_REALTIME | TICK_COLLECTRAW | TICK_FEED_STATS|TICK_NEGATIVE_PRICES
}
