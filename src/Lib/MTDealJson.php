<?php
namespace Aleedhillon\MetaTraderClient\Lib;

class MTDealJson
{
    /**
     * Get MTDeal from json object
     * @param object $obj
     * @return MTDeal
     */
    public static function GetFromJson($obj)
    {
        if ($obj == null)
            return null;
        $info = new MTDeal();
        //---
        $info->Deal = (int) $obj->Deal;
        $info->ExternalID = (string) $obj->ExternalID;
        $info->Login = (int) $obj->Login;
        $info->Dealer = (int) $obj->Dealer;
        $info->Order = (int) $obj->Order;
        $info->Action = (int) $obj->Action;
        $info->Entry = (int) $obj->Entry;
        $info->Reason = (int) $obj->Reason;
        $info->Digits = (int) $obj->Digits;
        $info->DigitsCurrency = (int) $obj->DigitsCurrency;
        $info->ContractSize = (float) $obj->ContractSize;
        $info->Time = (int) $obj->Time;
        $info->TimeMsc = (int) $obj->TimeMsc;
        $info->Symbol = (string) $obj->Symbol;
        $info->Price = (float) $obj->Price;
        $info->Volume = (int) $obj->Volume;
        if (isset($obj->VolumeExt))
            $info->VolumeExt = (int) $obj->VolumeExt;
        else
            $info->VolumeExt = (int) MTUtils::ToNewVolume($info->Volume);
        $info->Profit = (float) $obj->Profit;
        $info->Storage = (float) $obj->Storage;
        $info->Commission = (float) $obj->Commission;
        $info->RateProfit = (float) $obj->RateProfit;
        $info->RateMargin = (float) $obj->RateMargin;
        $info->ExpertID = (int) $obj->ExpertID;
        $info->PositionID = (int) $obj->PositionID;
        $info->Comment = (string) $obj->Comment;
        $info->ProfitRaw = (float) $obj->ProfitRaw;
        $info->PricePosition = (float) $obj->PricePosition;
        if (isset($obj->PriceSL))
            $info->PriceSL = (float) $obj->PriceSL;
        if (isset($obj->PriceTP))
            $info->PriceTP = (float) $obj->PriceTP;
        $info->VolumeClosed = (int) $obj->VolumeClosed;
        if (isset($obj->VolumeClosedExt))
            $info->VolumeClosedExt = (int) $obj->VolumeClosedExt;
        else
            $info->VolumeClosedExt = (int) MTUtils::ToNewVolume($info->VolumeClosed);
        $info->TickValue = (float) $obj->TickValue;
        $info->TickSize = (float) $obj->TickSize;
        $info->Flags = (int) $obj->Flags;
        $info->Gateway = (string) $obj->Gateway;
        $info->PriceGateway = (float) $obj->PriceGateway;
        $info->ModifyFlags = (int) $obj->ModifyFlags;
        //---
        return $info;
    }
}
