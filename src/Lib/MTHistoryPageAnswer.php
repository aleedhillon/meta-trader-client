<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * get history page answer
 **/
class MTHistoryPageAnswer
{
    public $RetCode = '-1';
    public $ConfigJson = '';
    /**
     * From json get class MTHistory
     * @return array(MTHistory)
     */
    public function GetArrayFromJson()
    {
        $objects = MTJson::Decode($this->ConfigJson);
        if ($objects == null)
            return null;
        $result = array();
        //---
        foreach ($objects as $obj) {
            $info = MTOrderJson::GetFromJson($obj);
            //---
            $result[] = $info;
        }
        //---
        $objects = null;
        //---
        return $result;
    }
}
