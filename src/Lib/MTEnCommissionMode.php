<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * commission charge mode
 */
class MTEnCommissionMode
{
    const COMM_MONEY_DEPOSIT = 0; // in money, in group deposit currency
    const COMM_MONEY_SYMBOL_BASE = 1; // in money, in symbol base currency
    const COMM_MONEY_SYMBOL_PROFIT = 2; // in money, in symbol profit currency
    const COMM_MONEY_SYMBOL_MARGIN = 3; // in money, in symbol margin currency
    const COMM_PIPS = 4; // in pips
    const COMM_PERCENT = 5; // in percent
    //--- enumeration borders
    const COMM_FIRST = MTEnCommissionMode::COMM_MONEY_DEPOSIT;
    const COMM_LAST = MTEnCommissionMode::COMM_PERCENT;
}
