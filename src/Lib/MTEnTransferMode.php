<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * EnTransferMode
 */
class MTEnTransferMode
{
    const TRANSFER_MODE_DISABLED = 0;
    const TRANSFER_MODE_NAME = 1;
    const TRANSFER_MODE_GROUP = 2;
    const TRANSFER_MODE_NAME_GROUP = 3;
    //--- enumeration borders
    const TRANSFER_MODE_FIRST = MTEnTransferMode::TRANSFER_MODE_DISABLED;
    const TRANSFER_MODE_LAST = MTEnTransferMode::TRANSFER_MODE_NAME_GROUP;
}
