<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * Requests Execution flags
 */
class MTEnREFlags
{
    const RE_FLAGS_NONE = 0; // none
    const RE_FLAGS_ORDER = 1; // confirm orders after price confirmation
    //--- enumeration borders
    const RE_FLAGS_ALL = MTEnREFlags::RE_FLAGS_ORDER;
}
