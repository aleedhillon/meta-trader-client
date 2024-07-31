<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * margin calculation flags
 */
class MTEnGroupMarginFlags
{
    const MARGIN_FLAGS_NONE = 0; // none
    const MARGIN_FLAGS_CLEAR_ACC = 1; // clear accumulated profit at end of day
    //--- enumeration borders
    const MARGIN_FLAGS_ALL = MTEnGroupMarginFlags::MARGIN_FLAGS_CLEAR_ACC;
}
