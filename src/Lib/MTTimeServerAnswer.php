<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * answer on request time_server
 */
class MTTimeServerAnswer
{
    public $RetCode = '-1';
    public $Time = 'none';
    /**
     * Get time in unix format
     * @return int
     */
    public function GetUnixTime()
    {
        $p = explode(" ", $this->Time, 2);
        return (int) $p[0];
    }
}
