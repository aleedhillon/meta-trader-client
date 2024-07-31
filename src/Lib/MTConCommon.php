<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * Common MetaTrader 5 Platform config
 */
class MTConCommon
{
    //--- server name
    public $Name;
    //--- owner full name (from license)
    public $Owner;
    //--- owner short name (from license)
    public $OwnerID;
    //--- owner host (from license)
    public $OwnerHost;
    //--- owner email (from license)
    public $OwnerEmail;
    //--- product full name (from license)
    public $Product;
    //--- license expiration date
    public $ExpirationLicense;
    //--- license support date
    public $ExpirationSupport;
    //--- max. trade servers count (from license)
    public $LimitTradeServers;
    //--- max. web servers count (from license)
    public $LimitWebServers;
    //--- max. real accounts count (from license)
    public $LimitAccounts;
    //--- max. trade deals count (from license)
    public $LimitDeals;
    //--- max. symbols count (from license)
    public $LimitSymbols;
    //--- max. client groups count (from license)
    public $LimitGroups;
    //--- LiveUpdate mode (type is MTEnUpdateMode)
    public $LiveUpdateMode;
    //--- Total users
    public $TotalUsers;
    //--- Total real users
    public $TotalUsersReal;
    //--- Total deals
    public $TotalDeals;
    //--- Total orders
    public $TotalOrders;
    //--- Total history orders
    public $TotalOrdersHistory;
    //--- Total positions
    public $TotalPositions;
    //--- Account Allocation URL
    public $AccountURL;
    //--- Account auto-allocation
    public $AccountAuto;
}
