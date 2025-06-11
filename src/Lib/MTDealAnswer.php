<?php

namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * get deal page answer
 */
class MTDealAnswer
{
    public $RetCode = '-1';
    public $ConfigJson = '';
    /**
     * From json get class MTDeal
     * @return MTDeal|null
     */
    public function GetFromJson()
    {
        $obj = MTJson::Decode($this->ConfigJson);
        if ($obj == null)
            return null;
        //---
        return MTDealJson::GetFromJson($obj);
    }
}
