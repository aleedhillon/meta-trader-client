<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * internal email modes
 */
class MTEnMailMode
{
    const MAIL_MODE_DISABLED = 0; // disable internal email
    const MAIL_MODE_FULL = 1; // enable internal email
    //--- enumeration borders
    const MAIL_MODE_FIRST = MTEnMailMode::MAIL_MODE_DISABLED;
    const MAIL_MODE_LAST = MTEnMailMode::MAIL_MODE_FULL;
}
