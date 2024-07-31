<?php
namespace Aleedhillon\MetaTraderClient\Lib;


class MTTickStatJson
{
    /**
     * Get MTTickState from json object
     * @param object $obj
     * @return MTTickStat
     */
    public static function GetFromJson($obj)
    {
        if ($obj == null)
            return null;
        $info = new MTTickStat();
        //---
        $info->Symbol = (string) $obj->Symbol;
        $info->Digits = (int) $obj->Digits;
        $info->Bid = (float) $obj->Bid;
        $info->BidLow = (float) $obj->BidLow;
        $info->BidHigh = (float) $obj->BidHigh;
        $info->BidDir = (int) $obj->BidDir;
        $info->Ask = (float) $obj->Ask;
        $info->AskLow = (float) $obj->AskLow;
        $info->AskHigh = (float) $obj->AskHigh;
        $info->AskDir = (int) $obj->AskDir;
        $info->Last = (float) $obj->Last;
        $info->LastLow = (float) $obj->LastLow;
        $info->LastHigh = (float) $obj->LastHigh;
        $info->LastDir = (int) $obj->LastDir;
        //---
        $info->Volume = (int) $obj->Volume;
        if (isset($obj->VolumeReal))
            $info->VolumeReal = (float) $obj->VolumeReal;
        else
            $info->VolumeReal = (float) $info->Volume;
        //---
        $info->VolumeLow = (int) $obj->VolumeLow;
        if (isset($obj->VolumeLowReal))
            $info->VolumeLowReal = (float) $obj->VolumeLowReal;
        else
            $info->VolumeLowReal = (float) $info->VolumeLow;
        //---
        $info->VolumeHigh = (int) $obj->VolumeHigh;
        if (isset($obj->VolumeHighReal))
            $info->VolumeHighReal = (float) $obj->VolumeHighReal;
        else
            $info->VolumeHighReal = (float) $info->VolumeHigh;
        //---
        $info->VolumeDir = (int) $obj->VolumeDir;
        $info->TradeDeals = (int) $obj->TradeDeals;
        //---
        $info->TradeVolume = (int) $obj->TradeVolume;
        if (isset($obj->TradeVolumeReal))
            $info->TradeVolumeReal = (float) $obj->TradeVolumeReal;
        else
            $info->TradeVolumeReal = (float) $info->TradeVolume;
        //---
        $info->TradeTurnover = (int) $obj->TradeTurnover;
        $info->TradeInterest = (int) $obj->TradeInterest;
        $info->TradeBuyOrders = (int) $obj->TradeBuyOrders;
        //---
        $info->TradeBuyVolume = (int) $obj->TradeBuyVolume;
        if (isset($obj->TradeBuyVolumeReal))
            $info->TradeBuyVolumeReal = (float) $obj->TradeBuyVolumeReal;
        else
            $info->TradeBuyVolumeReal = (float) $info->TradeBuyVolume;
        //---
        $info->TradeSellOrders = (int) $obj->TradeSellOrders;
        //---
        $info->TradeSellVolume = (int) $obj->TradeSellVolume;
        if (isset($obj->TradeSellVolumeReal))
            $info->TradeSellVolumeReal = (float) $obj->TradeSellVolumeReal;
        else
            $info->TradeSellVolumeReal = (float) $info->TradeSellVolume;
        //---
        $info->PriceOpen = (float) $obj->PriceOpen;
        $info->PriceClose = (float) $obj->PriceClose;
        $info->PriceChange = (float) $obj->PriceChange;
        $info->PriceVolatility = (float) $obj->PriceVolatility;
        $info->PriceTheoretical = (float) $obj->PriceTheoretical;
        //---
        return $info;
    }
}
