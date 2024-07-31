<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * reports generation flags
 */
class MTEnReportsFlags
{
    const REPORTSFLAGS_NONE = 0; // none
    const REPORTSFLAGS_EMAIL = 1; // send reports through email
    const REPORTSFLAGS_SUPPORT = 2; // send reports copies on support email
    const REPORTSFLAGS_STATEMENTS = 4; // generate reports
    //--- enumeration borders
    const REPORTSFLAGS_ALL = 7;
}
