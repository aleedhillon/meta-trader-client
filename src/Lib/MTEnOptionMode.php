<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * Options Mode
 * Class MTEnOptionMode
 */
class MTEnOptionMode
{
    const OPTION_MODE_EUROPEAN_CALL = 0;
    const OPTION_MODE_EUROPEAN_PUT = 1;
    const OPTION_MODE_AMERICAN_CALL = 2;
    const OPTION_MODE_AMERICAN_PUT = 3;
    //--- enumeration borders
    const OPTION_MODE_FIRST = MTEnOptionMode::OPTION_MODE_EUROPEAN_CALL;
    const OPTION_MODE_LAST = MTEnOptionMode::OPTION_MODE_AMERICAN_PUT;
}
