<?php

namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * Class answer from server for request user account get
 */
class MTUserAccountAnswer
{
    public $RetCode = '-1';
    public $Login = '';
    public $ConfigJson = '';

    /**
     * From json get class MTUser
     * @return MTUser
     */
    public function GetFromJson()
    {
        $obj = MTJson::Decode($this->ConfigJson);
        if ($obj == null)
            return null;
        $result = new MTAccount();
        //---
        $result->Login = isset($obj->Login) ? (int) $obj->Login : 0;
        $result->CurrencyDigits = isset($obj->CurrencyDigits) ? (int) $obj->CurrencyDigits : 2;
        $result->Balance = isset($obj->Balance) ? (float) $obj->Balance : 0.0;
        $result->Credit = isset($obj->Credit) ? (float) $obj->Credit : 0.0;
        $result->Margin = isset($obj->Margin) ? (float) $obj->Margin : 0.0;
        $result->MarginFree = isset($obj->MarginFree) ? (float) $obj->MarginFree : 0.0;
        $result->MarginLevel = isset($obj->MarginLevel) ? (float) $obj->MarginLevel : 0.0;
        $result->MarginLeverage = isset($obj->MarginLeverage) ? (int) $obj->MarginLeverage : 0;
        $result->Profit = isset($obj->Profit) ? (float) $obj->Profit : 0.0;
        $result->Storage = isset($obj->Storage) ? (float) $obj->Storage : 0.0;
        $result->Commission = isset($obj->Commission) ? (float) $obj->Commission : 0.0;
        $result->Floating = isset($obj->Floating) ? (float) $obj->Floating : 0.0;
        $result->Equity = isset($obj->Equity) ? (float) $obj->Equity : 0.0;
        $result->SOActivation = isset($obj->SOActivation) ? (int) $obj->SOActivation : 0;
        $result->SOTime = isset($obj->SOTime) ? (int) $obj->SOTime : 0;
        $result->SOLevel = isset($obj->SOLevel) ? (float) $obj->SOLevel : 0.0;
        $result->SOEquity = isset($obj->SOEquity) ? (float) $obj->SOEquity : 0.0;
        $result->SOMargin = isset($obj->SOMargin) ? (float) $obj->SOMargin : 0.0;
        $result->Assets = isset($obj->Assets) ? (float) $obj->Assets : 0.0;
        $result->Liabilities = isset($obj->Liabilities) ? (float) $obj->Liabilities : 0.0;
        $result->BlockedCommission = isset($obj->BlockedCommission) ? (float) $obj->BlockedCommission : 0.0;
        $result->BlockedProfit = isset($obj->BlockedProfit) ? (float) $obj->BlockedProfit : 0.0;
        $result->MarginInitial = isset($obj->MarginInitial) ? (float) $obj->MarginInitial : 0.0;
        $result->MarginMaintenance = isset($obj->MarginMaintenance) ? (float) $obj->MarginMaintenance : 0.0;

        // Add any additional properties that might be in the MTAccount class but not in the response
        if (isset($obj->Currency)) {
            $result->Currency = (string) $obj->Currency;
        }

        //---
        $obj = null;
        //---
        return $result;
    }
}
