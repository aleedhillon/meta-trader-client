<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * Request Execution Flags
 */
class MTEnRequestFlags
{
    const REQUEST_FLAGS_NONE = 0; // node
    const REQUEST_FLAGS_ORDER = 1; // trade orders should be additional confirmed after quotation
    //--- flags borders
    const REQUEST_FLAGS_ALL = MTEnRequestFlags::REQUEST_FLAGS_ORDER;
}
