<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * modification flags
 */
class MTEnTradeModifyFlags
{
    const MODIFY_FLAGS_ADMIN = 1;
    const MODIFY_FLAGS_MANAGER = 2;
    const MODIFY_FLAGS_POSITION = 4;
    const MODIFY_FLAGS_RESTORE = 8;
    const MODIFY_FLAGS_API_ADMIN = 16;
    const MODIFY_FLAGS_API_MANAGER = 32;
    const MODIFY_FLAGS_API_SERVER = 64;
    const MODIFY_FLAGS_API_GATEWAY = 128;
    const MODIFY_FLAGS_API_SERVER_ADD = 256;
    //--- enumeration borders
    const MODIFY_FLAGS_NONE = 0;
    const MODIFY_FLAGS_ALL = 511;
}
