<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * position types
 */
class MTEnPositionAction
{
    const POSITION_BUY = 0; // buy
    const POSITION_SELL = 1; // sell
    //--- enumeration borders
    const POSITION_FIRST = MTEnPositionAction::POSITION_BUY;
    const POSITION_LAST = MTEnPositionAction::POSITION_SELL;
}
