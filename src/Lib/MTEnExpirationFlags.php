<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * allowed order expiration modes flags
 */
class MTEnExpirationFlags
{
    const TIME_FLAGS_NONE = 0; // none
    const TIME_FLAGS_GTC = 1; // allowed Good Till Cancel
    const TIME_FLAGS_DAY = 2; // allowed Good Till Day
    const TIME_FLAGS_SPECIFIED = 4; // allowed specified expiration date
    const TIME_FLAGS_SPECIFIED_DAY = 8; // allowed specified expiration date as day
    //--- flags borders
    const TIME_FLAGS_FIRST = MTEnExpirationFlags::TIME_FLAGS_GTC;
    const TIME_FLAGS_ALL = 15; // TIME_FLAGS_GTC|TIME_FLAGS_DAY|TIME_FLAGS_SPECIFIED|TIME_FLAGS_SPECIFIED_DAY
}
