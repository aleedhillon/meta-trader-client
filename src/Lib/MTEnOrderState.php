<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * order state
 */
class MTEnOrderState
{
    const ORDER_STATE_STARTED = 0; // order started
    const ORDER_STATE_PLACED = 1; // order placed in system
    const ORDER_STATE_CANCELED = 2; // order canceled by client
    const ORDER_STATE_PARTIAL = 3; // order partially filled
    const ORDER_STATE_FILLED = 4; // order filled
    const ORDER_STATE_REJECTED = 5; // order rejected
    const ORDER_STATE_EXPIRED = 6; // order expired
    const ORDER_STATE_REQUEST_ADD = 7;
    const ORDER_STATE_REQUEST_MODIFY = 8;
    const ORDER_STATE_REQUEST_CANCEL = 9;
    //--- enumeration borders
    const ORDER_STATE_FIRST = MTEnOrderState::ORDER_STATE_STARTED;
    const ORDER_STATE_LAST = MTEnOrderState::ORDER_STATE_REQUEST_CANCEL;
}
