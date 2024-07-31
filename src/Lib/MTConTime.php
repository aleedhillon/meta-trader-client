<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * Time configuration
 */
class MTConTime
{
    //--- daylight correction mode
    public $Daylight;
    public $DaylightState;
    //--- server timezone in minutes (0-GMT;-3600=GMT-1;3600=GMT+1)
    public $TimeZone;
    //--- time synchronization server address (TIME or NTP protocol)
    public $TimeServer;
    //--- days
    public $Days;
}
