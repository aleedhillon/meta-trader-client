<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * modification flags
 */
class MTEnOrderTradeModifyFlags
{
    const MODIFY_FLAGS_ADMIN = 0x001;
    const MODIFY_FLAGS_MANAGER = 0x002;
    const MODIFY_FLAGS_POSITION = 0x004;
    const MODIFY_FLAGS_RESTORE = 0x008;
    const MODIFY_FLAGS_API_ADMIN = 0x010;
    const MODIFY_FLAGS_API_MANAGER = 0x020;
    const MODIFY_FLAGS_API_SERVER = 0x040;
    const MODIFY_FLAGS_API_GATEWAY = 0x080;
    const MODIFY_FLAGS_API_SERVER_ADD = 0x100;
    //--- enumeration borders
    const MODIFY_FLAGS_NONE = 0x000;
    const MODIFY_FLAGS_ALL = 0x1FF;
}
