<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * order types
 */
class MTEnOrderType
{
    const OP_BUY = 0; // buy order
    const OP_SELL = 1; // sell order
    const OP_BUY_LIMIT = 2; // buy limit order
    const OP_SELL_LIMIT = 3; // sell limit order
    const OP_BUY_STOP = 4; // buy stop order
    const OP_SELL_STOP = 5; // sell stop order
    const OP_BUY_STOP_LIMIT = 6; // buy stop limit order
    const OP_SELL_STOP_LIMIT = 7; // sell stop limit order
    const OP_CLOSE_BY = 8; // close by
    //--- enumeration borders
    const OP_FIRST = MTEnOrderType::OP_BUY;
    const OP_LAST = MTEnOrderType::OP_CLOSE_BY;
}
