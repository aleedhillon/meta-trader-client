<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * group risk management mode
 */
class MTEnMarginMode
{
    const MARGIN_MODE_RETAIL = 0;  // Retail FX, Retail CFD, Retail Futures
    const MARGIN_MODE_EXCHANGE_DISCOUNT = 1;  // Exchange, margin discount rates based
    const MARGIN_MODE_RETAIL_HEDGED = 2;  // Retail FX, Retail CFD, Retail Futures with hedged positions
    //--- enumeration borders
    const MARGIN_MODE_FIRST = MTEnMarginMode::MARGIN_MODE_RETAIL;
    const MARGIN_MODE_LAST = MTEnMarginMode::MARGIN_MODE_RETAIL_HEDGED;
}
