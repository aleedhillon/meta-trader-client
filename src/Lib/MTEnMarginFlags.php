<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * margin check modes
 */
class MTEnMarginFlags
{
    const MARGIN_FLAGS_NONE = 0; // none
    const MARGIN_FLAGS_CHECK_PROCESS = 1; // check margin after dealer confirmation
    const MARGIN_FLAGS_CHECK_SLTP = 2; // check margin on SL-TP trigger
    const MARGIN_FLAGS_HEDGE_LARGE_LEG = 4;  // check margin for hedged positions using large leg
    //--- enumeration borders
    const MARGIN_FLAGS_FIRST = MTEnMarginFlags::MARGIN_FLAGS_NONE;
    const MARGIN_FLAGS_LAST = MTEnMarginFlags::MARGIN_FLAGS_HEDGE_LARGE_LEG;
}
