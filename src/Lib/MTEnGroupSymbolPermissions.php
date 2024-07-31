<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * permissions
 */
class MTEnGroupSymbolPermissions
{
    const PERMISSION_NONE = 0;
    const PERMISSION_BOOK = 1;
    //--- enumeration borders
    const PERMISSION_DEFAULT = MTEnGroupSymbolPermissions::PERMISSION_BOOK;
    const PERMISSION_ALL = MTEnGroupSymbolPermissions::PERMISSION_BOOK;
}
