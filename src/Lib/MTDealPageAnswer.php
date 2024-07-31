<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * get deal page answer
 */
class MTDealPageAnswer
{
    public $RetCode = '-1';
    public $ConfigJson = '';
    /**
     * From json get class MTDeal
     * @return array(MTDeal)
     */
    public function GetArrayFromJson()
    {
        $objects = MTJson::Decode($this->ConfigJson);
        if ($objects == null)
            return null;
        $result = array();
        //---
        foreach ($objects as $obj) {
            $info = MTDealJson::GetFromJson($obj);
            //---
            $result[] = $info;
        }
        //---
        $objects = null;
        //---
        return $result;
    }
}
