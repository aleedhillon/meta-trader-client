<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * profit and margin calculation modes
 */
class MTEnCalcMode
{
    //--- market maker modes
    const TRADE_MODE_FOREX = 0;
    const TRADE_MODE_FUTURES = 1;
    const TRADE_MODE_CFD = 2;
    const TRADE_MODE_CFDINDEX = 3;
    const TRADE_MODE_CFDLEVERAGE = 4;
    const TRADEMODE_FOREX_NO_LEVERAGE = 5;
    //--- market makers enumerations
    const TRADE_MODE_MM_FIRST = MTEnCalcMode::TRADE_MODE_FOREX;
    const TRADE_MODE_MM_LAST = MTEnCalcMode::TRADEMODE_FOREX_NO_LEVERAGE;
    //--- exchange modes
    const TRADE_MODE_EXCH_STOCKS = 32;
    const TRADE_MODE_EXCH_FUTURES = 33;
    const TRADE_MODE_EXCH_FUTURES_FORTS = 34;
    const TRADE_MODE_EXCH_OPTIONS = 35;
    const TRADE_MODE_EXCH_OPTIONS_MARGIN = 36;
    const TRADE_MODE_EXCH_BONDS = 37;
    const TRADE_MODE_EXCH_STOCKS_MOEX = 38;
    const TRADE_MODE_EXCH_BONDS_MOEX = 39;
    //--- exchange enumerations
    const TRADE_MODE_EXCH_FIRST = MTEnCalcMode::TRADE_MODE_EXCH_STOCKS;
    const TRADE_MODE_EXCH_LAST = MTEnCalcMode::TRADE_MODE_EXCH_BONDS_MOEX;
    //--- service modes
    const TRADE_MODE_SERV_COLLATERAL = 64;
    //--- service enumerations
    const TRADE_MODE_SERV_FIRST = MTEnCalcMode::TRADE_MODE_SERV_COLLATERAL;
    const TRADE_MODE_SERV_LAST = MTEnCalcMode::TRADE_MODE_SERV_COLLATERAL;
    //--- enumeration borders
    const TRADE_MODE_FIRST = MTEnCalcMode::TRADE_MODE_FOREX;
    const TRADE_MODE_LAST = MTEnCalcMode::TRADE_MODE_SERV_COLLATERAL;
}
