<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * group permissions flags
 */
class MTEnPermissionsFlags
{
    const PERMISSION_NONE = 0; // default
    const PERMISSION_CERT_CONFIRM = 1; // certificate confirmation neccessary
    const PERMISSION_ENABLE_CONNECTION = 2; // clients connections allowed
    const PERMISSION_RESET_PASSWORD = 4; // reset password after first logon
    const PERMISSION_FORCED_OTP_USAGE = 8;  // forced usage OTP
    const PERMISSION_RISK_WARNING = 16; // show risk warning window on start
    const PERMISSION_REGULATION_PROTECT = 32; // country-specific regulatory protection
    //--- enumeration borders
    const PERMISSION_ALL = 63;
}
