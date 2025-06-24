<?php

namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * get position page answer
 */
class MTPositionAnswer
{
    public $RetCode = '-1';
    public $ConfigJson = '';
    /**
     * From json get class MTPosition
     * @return MTPosition|null
     */
    public function GetFromJson(): ?MTPosition
    {
        $obj = MTJson::Decode($this->ConfigJson);
        if ($obj == null)
            return null;
        //---
        return MTPositionJson::GetFromJson($obj);
    }
}
