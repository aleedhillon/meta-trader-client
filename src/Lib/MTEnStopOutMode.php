<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * stop-out mode
 */
class MTEnStopOutMode
{
    const STOPOUT_PERCENT = 0; // stop-out in percent
    const STOPOUT_MONEY = 1; // stop-out in money
    //--- enumeration borders
    const STOPOUT_FIRST = MTEnStopOutMode::STOPOUT_PERCENT;
    const STOPOUT_LAST = MTEnStopOutMode::STOPOUT_MONEY;
}
