<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * get history page answer
 */
class MTHistoryAnswer
{
    public $RetCode = '-1';
    public $ConfigJson = '';
    /**
     * From json get class MTOrder
     * @return array(MTOrder)
     */
    public function GetFromJson()
    {
        $obj = MTJson::Decode($this->ConfigJson);
        if ($obj == null)
            return null;
        //---
        return MTOrderJson::GetFromJson($obj);
    }
}
