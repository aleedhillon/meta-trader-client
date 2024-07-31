<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * Splice Type
 * Class MTEnSpliceType
 */
class MTEnSpliceType
{
    const SPLICE_NONE = 0;
    const SPLICE_UNADJUSTED = 1;
    const SPLICE_ADJUSTED = 2;
    //--- enumeration borders
    const SPLICE_FIRST = MTEnSpliceType::SPLICE_NONE;
    const SPLICE_LAST = MTEnSpliceType::SPLICE_ADJUSTED;
}
