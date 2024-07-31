<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * reports generation mode
 */
class MTEnReportsMode
{
    const REPORTS_DISABLED = 0; // reports disabled
    const REPORTS_STANDARD = 1; // standard mode
    //--- enumeration borders
    const REPORTS_FIRST = MTEnReportsMode::REPORTS_DISABLED;
    const REPORTS_LAST = MTEnReportsMode::REPORTS_STANDARD;
}
