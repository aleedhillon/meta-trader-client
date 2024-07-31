<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * trade rights flags
 */
class MTEnGroupTradeFlags
{
    const TRADEFLAGS_NONE = 0;   // none
    const TRADEFLAGS_SWAPS = 1;   // allow swaps charges
    const TRADEFLAGS_TRAILING = 2;   // allow trailing stops
    const TRADEFLAGS_EXPERTS = 4;   // allow expert advisors
    const TRADEFLAGS_EXPIRATION = 8;   // allow orders expiration
    const TRADEFLAGS_SIGNALS_ALL = 16;  // allow trade signals
    const TRADEFLAGS_SIGNALS_OWN = 32;  // allow trade signals only from own server
    const TRADEFLAGS_SO_COMPENSATION = 64;  // allow negative balance compensation after stop out
    //--- enumeration borders
    const TRADEFLAGS_DEFAULT = 31;
    const TRADEFLAGS_ALL = 127;
}
