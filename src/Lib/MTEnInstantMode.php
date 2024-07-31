<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * Instant Execution Modes
 */
class MTEnInstantMode
{
    const INSTANT_CHECK_NORMAL = 0;
    //--- begin and end of check
    const INSTANT_CHECK_FIRST = MTEnInstantMode::INSTANT_CHECK_NORMAL;
    const INSTANT_CHECK_LAST = MTEnInstantMode::INSTANT_CHECK_NORMAL;
}
