<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * Tick stat
 */
class MTTickStat
{
    //--- symbol
    public $Symbol;
    //--- digits
    public $Digits;
    //--- bid
    public $Bid;
    public $BidLow;
    public $BidHigh;
    public $BidDir;
    //--- ask
    public $Ask;
    public $AskLow;
    public $AskHigh;
    public $AskDir;
    //--- last price
    public $Last;
    public $LastLow;
    public $LastHigh;
    public $LastDir;
    //--- volume
    public $Volume;
    public $VolumeReal;
    public $VolumeLow;
    public $VolumeLowReal;
    public $VolumeHigh;
    public $VolumeHighReal;
    public $VolumeDir;
    //--- trade
    public $TradeDeals;
    public $TradeVolume;
    public $TradeVolumeReal;
    public $TradeTurnover;
    public $TradeInterest;
    public $TradeBuyOrders;
    public $TradeBuyVolume;
    public $TradeBuyVolumeReal;
    public $TradeSellOrders;
    public $TradeSellVolume;
    public $TradeSellVolumeReal;
    //--- price
    public $PriceOpen;
    public $PriceClose;
    public $PriceChange;
    public $PriceVolatility;
    public $PriceTheoretical;
}
