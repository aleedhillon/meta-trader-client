<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * answer on request common_get
 */
class MTCommonGetAnswer
{
    public $RetCode = '-1';
    public $ConfigJson = '';
    /**
     * From json get class MTConCommon
     * @return MTConTime
     */
    public function GetFromJson()
    {
        $obj = MTJson::Decode($this->ConfigJson);
        if ($obj == null)
            return null;
        //---
        $result = new MTConCommon();
        //---
        $result->Name = (string) $obj->Name;
        $result->Owner = (string) $obj->Owner;
        $result->OwnerID = (string) $obj->OwnerID;
        $result->OwnerHost = (string) $obj->OwnerHost;
        $result->OwnerEmail = (string) $obj->OwnerEmail;
        $result->Product = (string) $obj->Product;
        $result->ExpirationLicense = (int) $obj->ExpirationLicense;
        $result->ExpirationSupport = (int) $obj->ExpirationSupport;
        $result->LimitTradeServers = (int) $obj->LimitTradeServers;
        $result->LimitWebServers = (int) $obj->LimitWebServers;
        $result->LimitAccounts = (int) $obj->LimitAccounts;
        $result->LimitDeals = (int) $obj->LimitDeals;
        $result->LimitSymbols = (int) $obj->LimitSymbols;
        $result->LimitGroups = (int) $obj->LimitGroups;
        $result->LiveUpdateMode = (int) $obj->LiveUpdateMode;
        $result->TotalUsers = (int) $obj->TotalUsers;
        $result->TotalUsersReal = (int) $obj->TotalUsersReal;
        $result->TotalDeals = (int) $obj->TotalDeals;
        $result->TotalOrders = (int) $obj->TotalOrders;
        $result->TotalOrdersHistory = (int) $obj->TotalOrdersHistory;
        $result->TotalPositions = (int) $obj->TotalPositions;
        $result->AccountURL = (string) $obj->AccountURL;
        $result->AccountAuto = (int) $obj->AccountAuto;
        //---
        $obj = null;
        return $result;
    }
}
