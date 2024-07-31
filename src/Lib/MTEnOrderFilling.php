<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * order filling types
 */
class MTEnOrderFilling
{
    const ORDER_FILL_FOK = 0; // fill or kill
    const ORDER_FILL_IOC = 1; // immediate or cancel
    const ORDER_FILL_RETURN = 2; // return order in queue
    //--- enumeration borders
    const ORDER_FILL_FIRST = MTEnOrderFilling::ORDER_FILL_FOK;
    const ORDER_FILL_LAST = MTEnOrderFilling::ORDER_FILL_RETURN;
}
