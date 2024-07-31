<?php
namespace Aleedhillon\MetaTraderClient\Lib;

class MTPositionJson
{
    /**
     * Get MTPosition from json object
     * @param object $obj
     * @return MTPosition
     */
    public static function GetFromJson($obj)
    {
        if ($obj == null)
            return null;
        $info = new MTPosition();
        //---
        $info->Position = (int) $obj->Position;
        $info->ExternalID = (string) $obj->ExternalID;
        $info->Login = (int) $obj->Login;
        $info->Dealer = (int) $obj->Dealer;
        $info->Symbol = (string) $obj->Symbol;
        $info->Action = (int) $obj->Action;
        $info->Digits = (int) $obj->Digits;
        $info->DigitsCurrency = (int) $obj->DigitsCurrency;
        $info->Reason = (int) $obj->Reason;
        $info->ContractSize = (float) $obj->ContractSize;
        $info->TimeCreate = (int) $obj->TimeCreate;
        $info->TimeUpdate = (int) $obj->TimeUpdate;
        $info->ModifyFlags = (int) $obj->ModifyFlags;
        $info->PriceOpen = (float) $obj->PriceOpen;
        $info->PriceCurrent = (float) $obj->PriceCurrent;
        $info->PriceSL = (float) $obj->PriceSL;
        $info->PriceTP = (float) $obj->PriceTP;
        $info->Volume = (int) $obj->Volume;
        if (isset($obj->VolumeExt))
            $info->VolumeExt = (int) $obj->VolumeExt;
        else
            $info->VolumeExt = MTUtils::ToNewVolume($info->Volume);
        $info->Profit = (float) $obj->Profit;
        $info->Storage = (float) $obj->Storage;
        $info->RateProfit = (float) $obj->RateProfit;
        $info->RateMargin = (float) $obj->RateMargin;
        $info->ExpertID = (int) $obj->ExpertID;
        $info->ExpertPositionID = (int) $obj->ExpertPositionID;
        $info->Comment = (string) $obj->Comment;
        $info->ActivationMode = (int) $obj->ActivationMode;
        $info->ActivationTime = (int) $obj->ActivationTime;
        $info->ActivationPrice = (float) $obj->ActivationPrice;
        $info->ActivationFlags = (int) $obj->ActivationFlags;
        //---
        return $info;
    }
}
