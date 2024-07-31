<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * hedging flags
 */
class MTEnHedgeFlags
{
    const HEDGE_FLAGS_NONE = 0; // all disabled
    const HEDGE_FLAGS_ALLOW_CLOSEBY = 1; // allow close by
    //--- flags borders
    const HEDGE_FLAGS_FIRST = MTEnHedgeFlags::HEDGE_FLAGS_ALLOW_CLOSEBY;
    const HEDGE_FLAGS_ALL = 1; // HEDGE_FLAGS_ALLOW_CLOSEBY
}
