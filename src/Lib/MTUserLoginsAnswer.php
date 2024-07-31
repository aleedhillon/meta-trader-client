<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * class answer from server for requests about user logins
 */
class MTUserLoginsAnswer
{
    public $RetCode = '-1';
    public $ConfigJson = '';

    /**
     * From json get array logins
     * @return array(int)
     */
    public function GetFromJson()
    {
        $objects = MTJson::Decode($this->ConfigJson);
        if ($objects == null)
            return null;
        $result = array();
        //---
        foreach ($objects as $obj) {
            //---
            $result[] = (int) $obj;
        }
        //---
        $objects = null;
        //---
        return $result;
    }
}
