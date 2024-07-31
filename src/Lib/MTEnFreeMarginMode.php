<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * free margin calculation modes
 */
class MTEnFreeMarginMode
{
    const FREE_MARGIN_NOT_USE_PL = 0; // don't use floating profit and loss
    const FREE_MARGIN_USE_PL = 1; // use floating profit and loss
    const FREE_MARGIN_PROFIT = 2; // use floating profit only
    const FREE_MARGIN_LOSS = 3; // use floating loss only
    //--- enumeration borders
    const FREE_MARGIN_FIRST = MTEnFreeMarginMode::FREE_MARGIN_NOT_USE_PL;
    const FREE_MARGIN_LAST = MTEnFreeMarginMode::FREE_MARGIN_LOSS;
}
