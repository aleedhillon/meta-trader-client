<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * day working mode
 */
class MTEnTimeTableMode
{
    const TIME_MODE_DISABLED = 0; // work enabled
    const TIME_MODE_ENABLED = 1; // work disabled
    //---
    const TIME_MODE_FIRST = MTEnTimeTableMode::TIME_MODE_DISABLED;
    const TIME_MODE_LAST = MTEnTimeTableMode::TIME_MODE_ENABLED;
}
