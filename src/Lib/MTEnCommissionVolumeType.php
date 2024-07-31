<?php
namespace Aleedhillon\MetaTraderClient\Lib;

//--- commission type by volume
class MTEnCommissionVolumeType
{
    const COMM_TYPE_DEAL = 0; // commission per deal
    const COMM_TYPE_VOLUME = 1; // commission per volume
    //--- enumeration borders
    const COMM_TYPE_FIRST = MTEnCommissionVolumeType::COMM_TYPE_DEAL;
    const COMM_TYPE_LAST = MTEnCommissionVolumeType::COMM_TYPE_VOLUME;
}
