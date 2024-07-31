<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * modification flags
 */
class MTPositionEnTradeModifyFlags
{
    const MODIFY_FLAGS_ADMIN = 0x01;
    const MODIFY_FLAGS_MANAGER = 0x02;
    const MODIFY_FLAGS_POSITION = 0x04;
    const MODIFY_FLAGS_RESTORE = 0x08;
    const MODIFY_FLAGS_API_ADMIN = 0x10;
    const MODIFY_FLAGS_API_MANAGER = 0x20;
    const MODIFY_FLAGS_API_SERVER = 0x40;
    const MODIFY_FLAGS_API_GATEWAY = 0x80;
    //--- enumeration borders
    const MODIFY_FLAGS_NONE = 0x00;
    const MODIFY_FLAGS_ALL = 0xFF;
}
