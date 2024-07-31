<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * allowed filling modes flags
 */
class MTEnFillingFlags
{
    const FILL_FLAGS_NONE = 0; // none
    const FILL_FLAGS_FOK = 1; // allowed FOK
    const FILL_FLAGS_IOC = 2; // allowed IOC
    //--- flags borders
    const FILL_FLAGS_FIRST = MTEnFillingFlags::FILL_FLAGS_FOK;
    const FILL_FLAGS_ALL = 3; //MTEnFillingFlags::FILL_FLAGS_FOK | MTEnFillingFlags::FILL_FLAGS_IOC;
}
