<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * Mode of calculation of the free margin of the fixed income
 */
class MTEnMarginFreeProfitMode
{
    const FREE_MARGIN_PROFIT_PL = 0; // both fixed loss and profit on free margin
    const FREE_MARGIN_PROFIT_LOSS = 1; // only fixed loss on free margin
    //--- enumeration borders
    const FREE_MARGIN_PROFIT_FIRST = MTEnMarginFreeProfitMode::FREE_MARGIN_PROFIT_PL;
    const FREE_MARGIN_PROFIT_LAST = MTEnMarginFreeProfitMode::FREE_MARGIN_PROFIT_LOSS;
}
