<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * client history limits
 */
class MTEnHistoryLimit
{
    const TRADE_HISTORY_ALL = 0; // unlimited
    const TRADE_HISTORY_MONTHS_1 = 1; // one month
    const TRADE_HISTORY_MONTHS_3 = 2; // 3 months
    const TRADE_HISTORY_MONTHS_6 = 3; // 6 months
    const TRADE_HISTORY_YEAR_1 = 4; // 1 year
    const TRADE_HISTORY_YEAR_2 = 5; // 2 years
    const TRADE_HISTORY_YEAR_3 = 6; // 3 years
    //--- enumeration borders
    const TRADE_HISTORY_FIRST = MTEnHistoryLimit::TRADE_HISTORY_ALL;
    const TRADE_HISTORY_LAST = MTEnHistoryLimit::TRADE_HISTORY_YEAR_3;
}
