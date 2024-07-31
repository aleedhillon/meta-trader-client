<?php
namespace Aleedhillon\MetaTraderClient\Lib;

//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2000-2021, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
enum MTRetCodeMatch: int
{
    //--- successfully codes

    case MT_RET_OK = 0;       // ok
    case MT_RET_OK_NONE = 1;       // ok; no data
//--- common errors
    case MT_RET_ERROR = 2;       // Common error
    case MT_RET_ERR_PARAMS = 3;       // Invalid parameters
    case MT_RET_ERR_DATA = 4;       // Invalid data
    case MT_RET_ERR_DISK = 5;       // Disk error
    case MT_RET_ERR_MEM = 6;       // Memory error
    case MT_RET_ERR_NETWORK = 7;       // Network error
    case MT_RET_ERR_PERMISSIONS = 8;       // Not enough permissions
    case MT_RET_ERR_TIMEOUT = 9;       // Operation timeout
    case MT_RET_ERR_CONNECTION = 10;      // No connection
    case MT_RET_ERR_NOSERVICE = 11;      // Service is not available
    case MT_RET_ERR_FREQUENT = 12;      // Too frequent requests
    case MT_RET_ERR_NOTFOUND = 13;      // Not found
    case MT_RET_ERR_PARTIAL = 14;      // Partial error
    case MT_RET_ERR_SHUTDOWN = 15;      // Server shutdown in progress
    case MT_RET_ERR_CANCEL = 16;      // Operation has been canceled
    case MT_RET_ERR_DUPLICATE = 17;      // Duplicate data
//--- authentication retcodes
    case MT_RET_AUTH_CLIENT_INVALID = 1000;    // Invalid terminal type
    case MT_RET_AUTH_ACCOUNT_INVALID = 1001;    // Invalid account
    case MT_RET_AUTH_ACCOUNT_DISABLED = 1002;    // Account disabled
    case MT_RET_AUTH_ADVANCED = 1003;    // Advanced authorization necessary
    case MT_RET_AUTH_CERTIFICATE = 1004;    // Certificate required
    case MT_RET_AUTH_CERTIFICATE_BAD = 1005;    // Invalid certificate
    case MT_RET_AUTH_NOTCONFIRMED = 1006;    // Certificate is not confirmed
    case MT_RET_AUTH_SERVER_INTERNAL = 1007;    // Attempt to connect to non-access server
    case MT_RET_AUTH_SERVER_BAD = 1008;    // Server isn't authenticated
    case MT_RET_AUTH_UPDATE_ONLY = 1009;    // Only updates available
    case MT_RET_AUTH_CLIENT_OLD = 1010;    // Client has old version
    case MT_RET_AUTH_MANAGER_NOCONFIG = 1011;    // Manager account doesn't have manager config
    case MT_RET_AUTH_MANAGER_IPBLOCK = 1012;    // IP address unallowed for manager
    case MT_RET_AUTH_GROUP_INVALID = 1013;    // Group is not initialized (server restart neccesary)
    case MT_RET_AUTH_CA_DISABLED = 1014;    // Certificate generation disabled
    case MT_RET_AUTH_INVALID_ID = 1015;    // Invalid or disabled server id [check server's id]
    case MT_RET_AUTH_INVALID_IP = 1016;    // Unallowed address [check server's ip address]
    case MT_RET_AUTH_INVALID_TYPE = 1017;    // Invalid server type [check server's id and type]
    case MT_RET_AUTH_SERVER_BUSY = 1018;    // Server is busy
    case MT_RET_AUTH_SERVER_CERT = 1019;    // Invalid server certificate
    case MT_RET_AUTH_ACCOUNT_UNKNOWN = 1020;    // Unknown account
    case MT_RET_AUTH_SERVER_OLD = 1021;    // Old server version
    case MT_RET_AUTH_SERVER_LIMIT = 1022;    // Server cannot be connected due to license limitation
    case MT_RET_AUTH_MOBILE_DISABLED = 1023;    // Mobile connection aren't allowed in server license
//--- config management retcodes
    case MT_RET_CFG_LAST_ADMIN = 2000;    // Last admin config deleting
    case MT_RET_CFG_LAST_ADMIN_GROUP = 2001;    // Last admin group cannot be deleted
    case MT_RET_CFG_NOT_EMPTY = 2003;    // Accounts or trades in group
    case MT_RET_CFG_INVALID_RANGE = 2004;    // Invalid accounts or trades ranges
    case MT_RET_CFG_NOT_MANAGER_LOGIN = 2005;    // Manager account is not from manager group
    case MT_RET_CFG_BUILTIN = 2006;    // Built-in protected config
    case MT_RET_CFG_DUPLICATE = 2007;    // Configuration duplicate
    case MT_RET_CFG_LIMIT_REACHED = 2008;    // Configuration limit reached
    case MT_RET_CFG_NO_ACCESS_TO_MAIN = 2009;    // Invalid network configuration
    case MT_RET_CFG_DEALER_ID_EXIST = 2010;    // Dealer with same ID exists
    case MT_RET_CFG_BIND_ADDR_EXIST = 2011;    // Bind address already exists
    case MT_RET_CFG_WORKING_TRADE = 2012;    // Attempt to delete working trade server
//--- client management retcodes
    case MT_RET_USR_LAST_ADMIN = 3001;    // Last admin account deleting
    case MT_RET_USR_LOGIN_EXHAUSTED = 3002;    // Logins range exhausted
    case MT_RET_USR_LOGIN_PROHIBITED = 3003;    // Login reserved at another server
    case MT_RET_USR_LOGIN_EXIST = 3004;    // Account already exists
    case MT_RET_USR_SUICIDE = 3005;    // Attempt of self-deletion
    case MT_RET_USR_INVALID_PASSWORD = 3006;    // Invalid account password
    case MT_RET_USR_LIMIT_REACHED = 3007;    // Users limit reached
    case MT_RET_USR_HAS_TRADES = 3008;    // Account has open trades
    case MT_RET_USR_DIFFERENT_SERVERS = 3009;    // Attempt to move account to different server
    case MT_RET_USR_DIFFERENT_CURRENCY = 3010;    // Attempt to move account to different currency group
    case MT_RET_USR_IMPORT_BALANCE = 3011;    // Account balance import error
    case MT_RET_USR_IMPORT_GROUP = 3012;    // Account import with invalid group
//--- trades management retcodes
    case MT_RET_TRADE_LIMIT_REACHED = 4001;    // Orders or deals limit reached
    case MT_RET_TRADE_ORDER_EXIST = 4002;    // Order already exists
    case MT_RET_TRADE_ORDER_EXHAUSTED = 4003;    // Orders range exhausted
    case MT_RET_TRADE_DEAL_EXHAUSTED = 4004;    // Deals range exhausted
    case MT_RET_TRADE_MAX_MONEY = 4005;    // Money limit reached
//--- report generation retcodes
    case MT_RET_REPORT_SNAPSHOT = 5001;    // Base snapshot error
    case MT_RET_REPORT_NOTSUPPORTED = 5002;    // Method doesn't support for this report
    case MT_RET_REPORT_NODATA = 5003;    // No report data
    case MT_RET_REPORT_TEMPLATE_BAD = 5004;    // Bad template
    case MT_RET_REPORT_TEMPLATE_END = 5005;    // End of template (template success processed)
    case MT_RET_REPORT_INVALID_ROW = 5006;    // Invalid row size
    case MT_RET_REPORT_LIMIT_REPEAT = 5007;    // Tag repeat limit reached
    case MT_RET_REPORT_LIMIT_REPORT = 5008;    // Report size limit reached
//--- price history reports retcodes
    case MT_RET_HST_SYMBOL_NOTFOUND = 6001;    // Symbol not found; try to restart history server
//--- trade request retcodes
    case MT_RET_REQUEST_INWAY = 10001;   // Request on the way
    case MT_RET_REQUEST_ACCEPTED = 10002;   // Request accepted
    case MT_RET_REQUEST_PROCESS = 10003;   // Request processed
    case MT_RET_REQUEST_REQUOTE = 10004;   // Request Requoted
    case MT_RET_REQUEST_PRICES = 10005;   // Request Prices
    case MT_RET_REQUEST_REJECT = 10006;   // Request rejected
    case MT_RET_REQUEST_CANCEL = 10007;   // Request canceled
    case MT_RET_REQUEST_PLACED = 10008;   // Order from requestplaced
    case MT_RET_REQUEST_DONE = 10009;   // Request executed
    case MT_RET_REQUEST_DONE_PARTIAL = 10010;   // Request executed partially
    case MT_RET_REQUEST_ERROR = 10011;   // Request common error
    case MT_RET_REQUEST_TIMEOUT = 10012;   // Request timeout
    case MT_RET_REQUEST_INVALID = 10013;   // Invalid request
    case MT_RET_REQUEST_INVALID_VOLUME = 10014;   // Invalid volume
    case MT_RET_REQUEST_INVALID_PRICE = 10015;   // Invalid price
    case MT_RET_REQUEST_INVALID_STOPS = 10016;   // Invalid stops or price
    case MT_RET_REQUEST_TRADE_DISABLED = 10017;   // Trade disabled
    case MT_RET_REQUEST_MARKET_CLOSED = 10018;   // Market closed
    case MT_RET_REQUEST_NO_MONEY = 10019;   // Not enough money
    case MT_RET_REQUEST_PRICE_CHANGED = 10020;   // Price changed
    case MT_RET_REQUEST_PRICE_OFF = 10021;   // No prices
    case MT_RET_REQUEST_INVALID_EXP = 10022;   // Invalid order expiration
    case MT_RET_REQUEST_ORDER_CHANGED = 10023;   // Order has been changed already
    case MT_RET_REQUEST_TOO_MANY = 10024;   // Too many trade requests
    case MT_RET_REQUEST_NO_CHANGES = 10025;   // Request doesn't contain changes
    case MT_RET_REQUEST_AT_DISABLED_SERVER = 10026; // AutoTrading disabled by server
    case MT_RET_REQUEST_AT_DISABLED_CLIENT = 10027; // AutoTrading disabled by client
    case MT_RET_REQUEST_LOCKED = 10028;     // Request locked by dealer
    case MT_RET_REQUEST_FROZED = 10029;     // Order or position frozen
    case MT_RET_REQUEST_INVALID_FILL = 10030;     // Unsupported filling mode
    case MT_RET_REQUEST_CONNECTION = 10031;     // No connection
    case MT_RET_REQUEST_ONLY_REAL = 10032;     // Allowed for real accounts only
    case MT_RET_REQUEST_LIMIT_ORDERS = 10033;     // Orders limit reached
    case MT_RET_REQUEST_LIMIT_VOLUME = 10034;     // Volume limit reached
//--- dealer retcodes
    case MT_RET_REQUEST_RETURN = 11000;     // Request returned in queue
    case MT_RET_REQUEST_DONE_CANCEL = 11001;     // Request partially filled; remainder has been canceled
    case MT_RET_REQUEST_REQUOTE_RETURN = 11002;     // Request requoted and returned in queue with new prices
//--- API retcodes
    case MT_RET_ERR_NOTIMPLEMENT = 12000;     // Not implement yet
    case MT_RET_ERR_NOTMAIN = 12001;     // Operation must be performed on main server
    case MT_RET_ERR_NOTSUPPORTED = 12002;     // Command doesn't supported
    case MT_RET_ERR_DEADLOCK = 12003;     // Operation canceled due possible deadlock
    case MT_RET_ERR_LOCKED = 12004;     // Operation on locked entity
//--- Messengers retcodes
    case MT_RET_MESSENGER_INVALID_PHONE = 14000;    // Invalid phone number
    case MT_RET_MESSENGER_NOT_MOBILE = 14001;     // Phone number isn't mobile
//--- Subscriptions retcodes
    case MT_RET_SUBS_NOT_FOUND = 15000;     // Subscription is not found
    case MT_RET_SUBS_NOT_FOUND_CFG = 15001;     // Subscription config is not found
    case MT_RET_SUBS_NOT_FOUND_USER = 15002;     // User for subscription is not found
    case MT_RET_SUBS_DISABLED = 15003;     // Subscription is disabled
    case MT_RET_SUBS_PERMISSION_USER = 15004;     // Subscription is not allowed for user
    case MT_RET_SUBS_PERMISSION_SUBSCRIBE = 15005;  // Subscribe is not allowed
    case MT_RET_SUBS_PERMISSION_UNSUBSCRIBE = 15006;// Unsubscribe is not allowed
    case MT_RET_SUBS_REAL_ONLY = 15007;    // Subscriptions are available for real users only
    /**
     * Get error string by code
     * @static
     * @param MTRetCode $error_code
     * @return string error
     */
    public static function GetError($error_code)
    {
        return match ($error_code) {
            self::MT_RET_OK => 'ok',
            self::MT_RET_OK_NONE => 'ok; no data',
            self::MT_RET_ERROR => 'Common error',
            self::MT_RET_ERR_PARAMS => 'Invalid parameters',
            self::MT_RET_ERR_DATA => 'Invalid data',
            self::MT_RET_ERR_DISK => 'Disk error',
            self::MT_RET_ERR_MEM => 'Memory error',
            self::MT_RET_ERR_NETWORK => 'Network error',
            self::MT_RET_ERR_PERMISSIONS => 'Not enough permissions',
            self::MT_RET_ERR_TIMEOUT => 'Operation timeout',
            self::MT_RET_ERR_CONNECTION => 'No connection',
            self::MT_RET_ERR_NOSERVICE => 'Service is not available',
            self::MT_RET_ERR_FREQUENT => 'Too frequent requests',
            self::MT_RET_ERR_NOTFOUND => 'Not found',
            self::MT_RET_ERR_PARTIAL => 'Partial error',
            self::MT_RET_ERR_SHUTDOWN => 'Server shutdown in progress',
            self::MT_RET_ERR_CANCEL => 'Operation has been canceled',
            self::MT_RET_ERR_DUPLICATE => 'Duplicate data',
                //---
            self::MT_RET_AUTH_CLIENT_INVALID => 'Invalid terminal type',
            self::MT_RET_AUTH_ACCOUNT_INVALID => 'Invalid account',
            self::MT_RET_AUTH_ACCOUNT_DISABLED => 'Account disabled',
            self::MT_RET_AUTH_ADVANCED => 'Advanced authorization necessary',
            self::MT_RET_AUTH_CERTIFICATE => 'Certificate required',
            self::MT_RET_AUTH_CERTIFICATE_BAD => 'Invalid certificate',
            self::MT_RET_AUTH_NOTCONFIRMED => 'Certificate is not confirmed',
            self::MT_RET_AUTH_SERVER_INTERNAL => 'Attempt to connect to non-access server',
            self::MT_RET_AUTH_SERVER_BAD => 'Server is not authenticated',
            self::MT_RET_AUTH_UPDATE_ONLY => 'Only updates available',
            self::MT_RET_AUTH_CLIENT_OLD => 'Client has old version',
            self::MT_RET_AUTH_MANAGER_NOCONFIG => 'Manager account does not have manager config',
            self::MT_RET_AUTH_MANAGER_IPBLOCK => 'IP address unallowed for manager',
            self::MT_RET_AUTH_GROUP_INVALID => 'Group is not initialized (server restart necessary)',
            self::MT_RET_AUTH_CA_DISABLED => 'Certificate generation disabled',
            self::MT_RET_AUTH_INVALID_ID => 'Invalid or disabled server id [check server\'s id]',
            self::MT_RET_AUTH_INVALID_IP => 'Unallowed address [check server\'s ip address]',
            self::MT_RET_AUTH_INVALID_TYPE => 'Invalid server type [check server\'s id and type]',
            self::MT_RET_AUTH_SERVER_BUSY => 'Server is busy',
            self::MT_RET_AUTH_SERVER_CERT => 'Invalid server certificate',
            self::MT_RET_AUTH_ACCOUNT_UNKNOWN => 'Unknown account',
            self::MT_RET_AUTH_SERVER_OLD => 'Old server version',
            self::MT_RET_AUTH_SERVER_LIMIT => 'Server cannot be connected due to license limitation',
            self::MT_RET_AUTH_MOBILE_DISABLED => 'Mobile connection aren\'t allowed in server license',
                //---
            self::MT_RET_CFG_LAST_ADMIN => 'Last admin config deleting',
            self::MT_RET_CFG_LAST_ADMIN_GROUP => 'Last admin group cannot be deleted',
            self::MT_RET_CFG_NOT_EMPTY => 'Accounts or trades in group',
            self::MT_RET_CFG_INVALID_RANGE => 'Invalid accounts or trades ranges',
            self::MT_RET_CFG_NOT_MANAGER_LOGIN => 'Manager account is not from manager group',
            self::MT_RET_CFG_BUILTIN => 'Built-in protected config',
            self::MT_RET_CFG_DUPLICATE => 'Configuration duplicate',
            self::MT_RET_CFG_LIMIT_REACHED => 'Configuration limit reached',
            self::MT_RET_CFG_NO_ACCESS_TO_MAIN => 'Invalid network configuration',
            self::MT_RET_CFG_DEALER_ID_EXIST => 'Dealer with same ID exists',
            self::MT_RET_CFG_BIND_ADDR_EXIST => 'Bind address already exists',
            self::MT_RET_CFG_WORKING_TRADE => 'Attempt to delete working trade server',
                //---
            self::MT_RET_USR_LAST_ADMIN => 'Last admin account deleting',
            self::MT_RET_USR_LOGIN_EXHAUSTED => 'Logins range exhausted',
            self::MT_RET_USR_LOGIN_PROHIBITED => 'Login reserved at another server',
            self::MT_RET_USR_LOGIN_EXIST => 'Account already exists',
            self::MT_RET_USR_SUICIDE => 'Attempt of self-deletion',
            self::MT_RET_USR_INVALID_PASSWORD => 'Invalid account password',
            self::MT_RET_USR_LIMIT_REACHED => 'Users limit reached',
            self::MT_RET_USR_HAS_TRADES => 'Account has open trades',
            self::MT_RET_USR_DIFFERENT_SERVERS => 'Attempt to move account to different server',
            self::MT_RET_USR_DIFFERENT_CURRENCY => 'Attempt to move account to different currency group',
            self::MT_RET_USR_IMPORT_BALANCE => 'Account balance import error',
            self::MT_RET_USR_IMPORT_GROUP => 'Account import with invalid group',
                //---
            self::MT_RET_TRADE_LIMIT_REACHED => 'Orders or deals limit reached',
            self::MT_RET_TRADE_ORDER_EXIST => 'Order already exists',
            self::MT_RET_TRADE_ORDER_EXHAUSTED => 'Orders range exhausted',
            self::MT_RET_TRADE_DEAL_EXHAUSTED => 'Deals range exhausted',
            self::MT_RET_TRADE_MAX_MONEY => 'Money limit reached',
                //---
            self::MT_RET_REPORT_SNAPSHOT => 'Base snapshot error',
            self::MT_RET_REPORT_NOTSUPPORTED => 'Method doesn\'t support for this report',
            self::MT_RET_REPORT_NODATA => 'No report data',
            self::MT_RET_REPORT_TEMPLATE_BAD => 'Bad template',
            self::MT_RET_REPORT_TEMPLATE_END => 'End of template (template success processed)',
            self::MT_RET_REPORT_INVALID_ROW => 'Invalid row size',
            self::MT_RET_REPORT_LIMIT_REPEAT => 'Tag repeat limit reached',
            self::MT_RET_REPORT_LIMIT_REPORT => 'Report size limit reached',
                //---
            self::MT_RET_HST_SYMBOL_NOTFOUND => 'Symbol not found; try to restart history server',
                //---
            self::MT_RET_REQUEST_INWAY => 'Request on the way',
            self::MT_RET_REQUEST_ACCEPTED => 'Request accepted',
            self::MT_RET_REQUEST_PROCESS => 'Request processed',
            self::MT_RET_REQUEST_REQUOTE => 'Request Requoted',
            self::MT_RET_REQUEST_PRICES => 'Request Prices',
            self::MT_RET_REQUEST_REJECT => 'Request rejected',
            self::MT_RET_REQUEST_CANCEL => 'Request canceled',
            self::MT_RET_REQUEST_PLACED => 'Order from request placed',
            self::MT_RET_REQUEST_DONE => 'Request executed',
            self::MT_RET_REQUEST_DONE_PARTIAL => 'Request executed partially',
            self::MT_RET_REQUEST_ERROR => 'Request common error',
            self::MT_RET_REQUEST_TIMEOUT => 'Request timeout',
            self::MT_RET_REQUEST_INVALID => 'Invalid request',
            self::MT_RET_REQUEST_INVALID_VOLUME => 'Invalid volume',
            self::MT_RET_REQUEST_INVALID_PRICE => 'Invalid price',
            self::MT_RET_REQUEST_INVALID_STOPS => 'Invalid stops or price',
            self::MT_RET_REQUEST_TRADE_DISABLED => 'Trade disabled',
            self::MT_RET_REQUEST_MARKET_CLOSED => 'Market closed',
            self::MT_RET_REQUEST_NO_MONEY => 'Not enough money',
            self::MT_RET_REQUEST_PRICE_CHANGED => 'Price changed',
            self::MT_RET_REQUEST_PRICE_OFF => 'No prices',
            self::MT_RET_REQUEST_INVALID_EXP => 'Invalid order expiration',
            self::MT_RET_REQUEST_ORDER_CHANGED => 'Order has been changed already',
            self::MT_RET_REQUEST_TOO_MANY => 'Too many trade requests',
            self::MT_RET_REQUEST_NO_CHANGES => 'Request doesn\'t contain changes',
            self::MT_RET_REQUEST_AT_DISABLED_SERVER => 'AutoTrading disabled by server',
            self::MT_RET_REQUEST_AT_DISABLED_CLIENT => 'AutoTrading disabled by client',
            self::MT_RET_REQUEST_LOCKED => 'Request locked by dealer',
            self::MT_RET_REQUEST_FROZED => 'Order or position frozen',
            self::MT_RET_REQUEST_INVALID_FILL => 'Unsupported filling mode',
            self::MT_RET_REQUEST_CONNECTION => 'No connection',
            self::MT_RET_REQUEST_ONLY_REAL => 'Allowed for real accounts only',
            self::MT_RET_REQUEST_LIMIT_ORDERS => 'Orders limit reached',
            self::MT_RET_REQUEST_LIMIT_VOLUME => 'Volume limit reached',
                //---
            self::MT_RET_REQUEST_RETURN => 'Request returned in queue',
            self::MT_RET_REQUEST_DONE_CANCEL => 'Request partially filled; remainder has been canceled',
            self::MT_RET_REQUEST_REQUOTE_RETURN => 'Request requoted and returned in queue with new prices',
            self::MT_RET_ERR_NOTIMPLEMENT => 'Not implement yet',
            self::MT_RET_ERR_NOTMAIN => 'Operation must be performed on main server',
            self::MT_RET_ERR_NOTSUPPORTED => 'Command doesn\'t supported',
            self::MT_RET_ERR_DEADLOCK => 'Operation canceled due possible deadlock',
            self::MT_RET_ERR_LOCKED => 'Operation on locked entity',
            self::MT_RET_MESSENGER_INVALID_PHONE => 'Invalid phone number',
            self::MT_RET_MESSENGER_NOT_MOBILE => 'Phone number isn\'t mobile',
            self::MT_RET_SUBS_NOT_FOUND => 'Subscription is not found',
            self::MT_RET_SUBS_NOT_FOUND_CFG => 'Subscription config is not found',
            self::MT_RET_SUBS_NOT_FOUND_USER => 'User for subscription is not found',
            self::MT_RET_SUBS_DISABLED => 'Subscription is disabled',
            self::MT_RET_SUBS_PERMISSION_USER => 'Subscription is not allowed for user',
            self::MT_RET_SUBS_PERMISSION_SUBSCRIBE => 'Subscribe is not allowed',
            self::MT_RET_SUBS_PERMISSION_UNSUBSCRIBE => 'Unsubscribe is not allowed',
            self::MT_RET_SUBS_REAL_ONLY => 'Subscriptions are available for real users only',
            default => 'Unknown error',
        };
    }
}
