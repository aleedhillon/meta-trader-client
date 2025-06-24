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
     * @return MTConCommon|null
     */
    public function GetFromJson()
    {
        $obj = MTJson::Decode($this->ConfigJson);
        if ($obj == null)
            return null;
        //---
        $result = new MTConCommon();
        //---
        $result->Name = (string) ($obj->Name ?? '');
        $result->Owner = (string) ($obj->Owner ?? '');
        $result->OwnerID = (string) ($obj->OwnerID ?? '');
        $result->OwnerHost = (string) ($obj->OwnerHost ?? '');
        $result->OwnerEmail = (string) ($obj->OwnerEmail ?? '');
        $result->Product = (string) ($obj->Product ?? '');
        $result->ExpirationLicense = (int) ($obj->ExpirationLicense ?? 0);
        $result->ExpirationSupport = (int) ($obj->ExpirationSupport ?? 0);
        $result->LimitTradeServers = (int) ($obj->LimitTradeServers ?? 0);
        $result->LimitWebServers = (int) ($obj->LimitWebServers ?? 0);
        $result->LimitAccounts = (int) ($obj->LimitAccounts ?? 0);
        $result->LimitDeals = (int) ($obj->LimitDeals ?? 0);
        $result->LimitSymbols = (int) ($obj->LimitSymbols ?? 0);
        $result->LimitGroups = (int) ($obj->LimitGroups ?? 0);
        $result->LiveUpdateMode = (int) ($obj->LiveUpdateMode ?? 0);
        $result->TotalUsers = (int) ($obj->TotalUsers ?? 0);
        $result->TotalUsersReal = (int) ($obj->TotalUsersReal ?? 0);
        $result->TotalDeals = (int) ($obj->TotalDeals ?? 0);
        $result->TotalOrders = (int) ($obj->TotalOrders ?? 0);
        $result->TotalOrdersHistory = (int) ($obj->TotalOrdersHistory ?? 0);
        $result->TotalPositions = (int) ($obj->TotalPositions ?? 0);
        $result->AccountURL = (string) ($obj->AccountURL ?? '');
        $result->AccountAuto = (int) ($obj->AccountAuto ?? 0);
        //---
        $obj = null;
        return $result;
    }
}
