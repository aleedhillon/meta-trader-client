<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * get position page answer
 */
class MTPositionPageAnswer
{
    public $RetCode = '-1';
    public $ConfigJson = '';
    /**
     * From json get class MTPosition
     * @return array(MTPosition)
     */
    public function GetArrayFromJson()
    {
        $objects = MTJson::Decode($this->ConfigJson);
        if ($objects == null)
            return null;
        $result = array();
        //---
        foreach ($objects as $obj) {
            $info = MTPositionJson::GetFromJson($obj);
            //---
            $result[] = $info;
        }
        //---
        $objects = null;
        //---
        return $result;
    }
}
