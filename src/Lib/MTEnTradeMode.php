<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * allowed trade modes
 */
class MTEnTradeMode
{
    const TRADE_DISABLED = 0; // trade disabled
    const TRADE_LONGONLY = 1; // only long positions allowed
    const TRADE_SHORTONLY = 2; // only short positions allowed
    const TRADE_CLOSEONLY = 3; // only positions closure
    const TRADE_FULL = 4; // all trade operations are allowed
    //--- enumeration borders
    const TRADE_FIRST = MTEnTradeMode::TRADE_DISABLED;
    const TRADE_LAST = MTEnTradeMode::TRADE_FULL;

    /**
     * Get object
     *
     * @param $id
     *
     * @return MTEnTradeMode
     */
    public static function Get($id)
    {
        $id = (int) $id;
        switch ($id) {
            case MTEnTradeMode::TRADE_DISABLED:
                return MTEnTradeMode::TRADE_DISABLED;
            //---
            case MTEnTradeMode::TRADE_LONGONLY:
                return MTEnTradeMode::TRADE_LONGONLY;
            //---
            case MTEnTradeMode::TRADE_SHORTONLY:
                return MTEnTradeMode::TRADE_SHORTONLY;
            //---
            case MTEnTradeMode::TRADE_CLOSEONLY:
                return MTEnTradeMode::TRADE_CLOSEONLY;
            //---
            case MTEnTradeMode::TRADE_FULL:
                return MTEnTradeMode::TRADE_FULL;
            //---
            case MTEnTradeMode::TRADE_FIRST:
                return MTEnTradeMode::TRADE_FIRST;
            //---
            case MTEnTradeMode::TRADE_LAST:
                return MTEnTradeMode::TRADE_LAST;
        }
    }
}
