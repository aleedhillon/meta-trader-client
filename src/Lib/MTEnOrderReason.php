<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * order creation reasons
 */
class MTEnOrderReason
{
    const ORDER_REASON_CLIENT = 0;  // order placed manually
    const ORDER_REASON_EXPERT = 1;  // order placed by expert
    const ORDER_REASON_DEALER = 2;  // order placed by dealer
    const ORDER_REASON_SL = 3;  // order placed due SL
    const ORDER_REASON_TP = 4;  // order placed due TP
    const ORDER_REASON_SO = 5;  // order placed due Stop-Out
    const ORDER_REASON_ROLLOVER = 6;  // order placed due rollover
    const ORDER_REASON_EXTERNAL_CLIENT = 7;  // order placed from the external system by client
    const ORDER_REASON_VMARGIN = 8;  // order placed due variation margin
    const ORDER_REASON_GATEWAY = 9;  // order placed by gateway
    const ORDER_REASON_SIGNAL = 10; // order placed by signal service
    const ORDER_REASON_SETTLEMENT = 11; // order placed by settlement
    const ORDER_REASON_TRANSFER = 12; // order placed due transfer
    const ORDER_REASON_SYNC = 13; // order placed due synchronization
    const ORDER_REASON_EXTERNAL_SERVICE = 14; // order placed from the external system due service issues
    const ORDER_REASON_MIGRATION = 15; // order placed due account migration from MetaTrader 4 or MetaTrader 5
    const ORDER_REASON_MOBILE = 16; // order placed manually by mobile terminal
    const ORDER_REASON_WEB = 17; // order placed manually by web terminal
    const ORDER_REASON_SPLIT = 18; // order placed due split
    //--- enumeration borders
    const ORDER_REASON_FIRST = MTEnOrderReason::ORDER_REASON_CLIENT;
    const ORDER_REASON_LAST = MTEnOrderReason::ORDER_REASON_SPLIT;
}
