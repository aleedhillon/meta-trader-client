<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * swaps calculation modes
 */
class MTEnSwapMode
{
    const SWAP_DISABLED = 0;
    const SWAP_BY_POINTS = 1;
    const SWAP_BY_SYMBOL_CURRENCY = 2;
    const SWAP_BY_MARGIN_CURRENCY = 3;
    const SWAP_BY_GROUP_CURRENCY = 4;
    const SWAP_BY_INTEREST_CURRENT = 5;
    const SWAP_BY_INTEREST_OPEN = 6;
    const SWAP_REOPEN_BY_CLOSE_PRICE = 7;
    const SWAP_REOPEN_BY_BID = 8;
    const SWAP_BY_PROFIT_CURRENCY = 9;
    //--- enumeration borders
    const SWAP_FIRST = MTEnSwapMode::SWAP_DISABLED;
    const SWAP_LAST = MTEnSwapMode::SWAP_BY_PROFIT_CURRENCY;
}
