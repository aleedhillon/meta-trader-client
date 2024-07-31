<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * activation method
 */
class MTEnSoActivation
{
    const ACTIVATION_NONE = 0;
    const ACTIVATION_MARGIN_CALL = 1;
    const ACTIVATION_STOP_OUT = 2;
    //---
    const ACTIVATION_FIRST = ACTIVATION_NONE;
    const ACTIVATION_LAST = ACTIVATION_STOP_OUT;
}
