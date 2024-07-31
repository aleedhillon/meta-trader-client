<?php
namespace Aleedhillon\MetaTraderClient\Lib;

class MTTickJson
{
    /**
     * Get MTTick from json object
     * @param object $obj
     * @return MTTick
     */
    public static function GetFromJson($obj)
    {
        if ($obj == null)
            return null;
        $info = new MTTick();
        //---
        $info->Symbol = (string) $obj->Symbol;
        $info->Digits = (int) $obj->Digits;
        $info->Bid = (float) $obj->Bid;
        $info->Ask = (float) $obj->Ask;
        $info->Last = (float) $obj->Last;
        $info->Volume = (int) $obj->Volume;
        if (isset($obj->VolumeReal))
            $info->VolumeReal = (float) $obj->VolumeReal;
        else
            $info->VolumeReal = (float) $info->Volume;
        //---
        return $info;
    }
}
