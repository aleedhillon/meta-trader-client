<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * economical sectors
 */
class EnSectors
{
    const SECTOR_UNDEFINED = 0;
    const SECTOR_BASIC_MATERIALS = 1;
    const SECTOR_COMMUNICATION_SERVICES = 2;
    const SECTOR_CONSUMER_CYCLICAL = 3;
    const SECTOR_CONSUMER_DEFENSIVE = 4;
    const SECTOR_ENERGY = 5;
    const SECTOR_FINANCIAL = 6;
    const SECTOR_HEALTHCARE = 7;
    const SECTOR_INDUSTRIALS = 8;
    const SECTOR_REAL_ESTATE = 9;
    const SECTOR_TECHNOLOGY = 10;
    const SECTOR_UTILITIES = 11;
    const SECTOR_CURRENCY = 12;
    const SECTOR_CURRENCY_CRYPTO = 13;
    const SECTOR_INDEXES = 14;
    const SECTOR_COMMODITIES = 15;
    //--- enumeration borders
    const SECTOR_FIRST = EnSectors::SECTOR_UNDEFINED;
    const SECTOR_LAST = EnSectors::SECTOR_COMMODITIES;
}
