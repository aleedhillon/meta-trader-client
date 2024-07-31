<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * position creation reasons
 */
class MTEnPositionReason
{
    const POSITION_REASON_CLIENT = 0;  // position placed manually
    const POSITION_REASON_EXPERT = 1;  // position placed by expert
    const POSITION_REASON_DEALER = 2;  // position placed by dealer
    const POSITION_REASON_SL = 3;  // position placed due SL
    const POSITION_REASON_TP = 4;  // position placed due TP
    const POSITION_REASON_SO = 5;  // position placed due Stop-Out
    const POSITION_REASON_ROLLOVER = 6;  // position placed due rollover
    const POSITION_REASON_EXTERNAL_CLIENT = 7;  // position placed from the external system by client
    const POSITION_REASON_VMARGIN = 8;  // position placed due variation margin
    const POSITION_REASON_GATEWAY = 9;  // position placed by gateway
    const POSITION_REASON_SIGNAL = 10; // position placed by signal service
    const POSITION_REASON_SETTLEMENT = 11; // position placed due settlement
    const POSITION_REASON_TRANSFER = 12; // position placed due position transfer
    const POSITION_REASON_SYNC = 13; // position placed due position synchronization
    const POSITION_REASON_EXTERNAL_SERVICE = 14; // position placed from the external system due service issues
    const POSITION_REASON_MIGRATION = 15; // position placed due migration
    const POSITION_REASON_MOBILE = 16; // position placed by mobile terminal
    const POSITION_REASON_WEB = 17; // position placed by web terminal
    const POSITION_REASON_SPLIT = 18; // position placed due split
    //--- enumeration borders
    const POSITION_REASON_FIRST = MTEnPositionReason::POSITION_REASON_CLIENT;
    const POSITION_REASON_LAST = MTEnPositionReason::POSITION_REASON_SPLIT;
}
