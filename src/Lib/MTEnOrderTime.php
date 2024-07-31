<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * order expiration types
 */
class MTEnOrderTime
{
    const ORDER_TIME_GTC = 0; // good till cancel
    const ORDER_TIME_DAY = 1; // good till day
    const ORDER_TIME_SPECIFIED = 2; // good till specified
    const ORDER_TIME_SPECIFIED_DAY = 3; // good till specified day
    //--- enumeration borders
    const ORDER_TIME_FIRST = MTEnOrderTime::ORDER_TIME_GTC;
    const ORDER_TIME_LAST = MTEnOrderTime::ORDER_TIME_SPECIFIED_DAY;
}
