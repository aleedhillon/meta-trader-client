<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * get group info
 */
class MTGroupAnswer
{
    public $RetCode = '-1';
    public $ConfigJson = '';

    /**
     * From json get class MTConGroup
     * @return MTConGroup
     */
    public function GetFromJson()
    {
        $obj = MTJson::Decode($this->ConfigJson);

        if ($obj == null)
            return null;

        $result = new MTConGroup();
        //---
        $result->Group = (string) $obj->Group;
        $result->Server = (int) $obj->Server;
        $result->PermissionsFlags = (int) $obj->PermissionsFlags;
        $result->AuthMode = (int) $obj->AuthMode;
        $result->AuthPasswordMin = (int) $obj->AuthPasswordMin;
        $result->AuthOTPMode = (int) $obj->AuthOTPMode;
        $result->Company = (string) $obj->Company;
        $result->CompanyPage = (string) $obj->CompanyPage;
        $result->CompanyEmail = (string) $obj->CompanyEmail;
        $result->CompanySupportPage = (string) $obj->CompanySupportPage;
        $result->CompanySupportEmail = (string) $obj->CompanySupportEmail;
        $result->CompanyCatalog = (string) $obj->CompanyCatalog;
        $result->Currency = (string) $obj->Currency;
        $result->CurrencyDigits = (int) $obj->CurrencyDigits;
        $result->ReportsMode = (int) $obj->ReportsMode;
        $result->ReportsFlags = (int) $obj->ReportsFlags;
        $result->ReportsSMTP = (string) $obj->ReportsSMTP;
        $result->ReportsSMTPLogin = (string) $obj->ReportsSMTPLogin;
        $result->ReportsSMTPPass = (string) $obj->ReportsSMTPPass;
        $result->NewsMode = (int) $obj->NewsMode;
        $result->NewsCategory = (string) $obj->NewsCategory;
        $result->NewsLangs = (array) $obj->NewsLangs;
        $result->MailMode = (int) $obj->MailMode;
        $result->TradeFlags = (int) $obj->TradeFlags;
        $result->TradeTransferMode = (int) $obj->TradeTransferMode;
        $result->TradeInterestrate = (float) $obj->TradeInterestrate;
        $result->TradeVirtualCredit = (float) $obj->TradeVirtualCredit;
        $result->MarginMode = (int) $obj->MarginMode;
        $result->MarginSOMode = (int) $obj->MarginSOMode;
        $result->MarginFreeMode = (int) $obj->MarginFreeMode;
        $result->MarginCall = (float) $obj->MarginCall;
        $result->MarginStopOut = (float) $obj->MarginStopOut;
        $result->MarginFreeProfitMode = (int) $obj->MarginFreeProfitMode;
        $result->DemoLeverage = (int) $obj->DemoLeverage;
        $result->DemoDeposit = (float) $obj->DemoDeposit;
        $result->LimitHistory = (int) $obj->LimitHistory;
        $result->LimitOrders = (int) $obj->LimitOrders;
        $result->LimitSymbols = (int) $obj->LimitSymbols;
        $result->LimitPositions = (int) $obj->LimitPositions;
        $result->Commissions = $obj->Commissions;
        //---
        $result->Symbols = array();
        //---
        foreach ($obj->Symbols as $symbol) {
            $s = new MTConGroupSymbol();
            //--- copy data from json object to MTConGroupSymbol
            $s->Path = (string) $symbol->Path;
            $s->TradeMode = $symbol->TradeMode == 'default' ? MTConGroupSymbol::GetDefault('trademode') : (int) $symbol->TradeMode;
            $s->ExecMode = $symbol->ExecMode == 'default' ? MTConGroupSymbol::GetDefault('execmode') : (int) $symbol->ExecMode;
            $s->FillFlags = $symbol->FillFlags == 'default' ? MTConGroupSymbol::GetDefault('fillflags') : (int) $symbol->FillFlags;
            $s->ExpirFlags = $symbol->ExpirFlags == 'default' ? MTConGroupSymbol::GetDefault('expirflags') : (int) $symbol->ExpirFlags;
            $s->OrderFlags = $symbol->OrderFlags == 'default' ? MTEnOrderFlags::ORDER_FLAGS_NONE : (int) $symbol->OrderFlags;
            $s->SpreadDiff = $symbol->SpreadDiff == 'default' ? MTConGroupSymbol::GetDefault('spreaddiff') : (int) $symbol->SpreadDiff;
            $s->SpreadDiffBalance = $symbol->SpreadDiffBalance == 'default' ? MTConGroupSymbol::GetDefault('spreaddiffbalance') : (int) $symbol->SpreadDiffBalance;
            $s->StopsLevel = $symbol->StopsLevel == 'default' ? MTConGroupSymbol::GetDefault('stopslevel') : (int) $symbol->StopsLevel;
            $s->FreezeLevel = $symbol->FreezeLevel == 'default' ? MTConGroupSymbol::GetDefault('freezelevel') : (int) $symbol->FreezeLevel;
            //---
            $s->VolumeMin = $symbol->VolumeMin == 'default' ? MTConGroupSymbol::GetDefault('volumemin') : (int) $symbol->VolumeMin;
            if (isset($symbol->VolumeMinExt))
                $s->VolumeMinExt = $symbol->VolumeMinExt == 'default' ? MTConGroupSymbol::GetDefault('volumemin') : (int) $symbol->VolumeMinExt;
            else
                $s->VolumeMinExt = $s->VolumeMin == MTConGroupSymbol::GetDefault('volumemin') ? $s->VolumeMin : MTUtils::ToNewVolume($s->VolumeMin);
            //---
            $s->VolumeMax = $symbol->VolumeMax == 'default' ? MTConGroupSymbol::GetDefault('volumemax') : (int) $symbol->VolumeMax;
            if (isset($symbol->VolumeMaxExt))
                $s->VolumeMaxExt = $symbol->VolumeMaxExt == 'default' ? MTConGroupSymbol::GetDefault('volumemax') : (int) $symbol->VolumeMaxExt;
            else
                $s->VolumeMaxExt = $s->VolumeMax == MTConGroupSymbol::GetDefault('volumemax') ? $s->VolumeMax : MTUtils::ToNewVolume($s->VolumeMax);
            //---
            $s->VolumeStep = $symbol->VolumeStep == 'default' ? MTConGroupSymbol::GetDefault('volumestep') : (int) $symbol->VolumeStep;
            if (isset($symbol->VolumeStepExt))
                $s->VolumeStepExt = $symbol->VolumeStepExt == 'default' ? MTConGroupSymbol::GetDefault('volumestep') : (int) $symbol->VolumeStepExt;
            else
                $s->VolumeStepExt = $s->VolumeStep == MTConGroupSymbol::GetDefault('volumestep') ? $s->VolumeStep : MTUtils::ToNewVolume($s->VolumeStep);
            //---
            $s->VolumeLimit = $symbol->VolumeLimit == 'default' ? MTConGroupSymbol::GetDefault('volumelimit') : (int) $symbol->VolumeLimit;
            if (isset($symbol->VolumeLimitExt))
                $s->VolumeLimitExt = $symbol->VolumeLimitExt == 'default' ? MTConGroupSymbol::GetDefault('volumelimit') : (int) $symbol->VolumeLimitExt;
            else
                $s->VolumeLimitExt = $s->VolumeLimit == MTConGroupSymbol::GetDefault('volumelimit') ? $s->VolumeLimit : MTUtils::ToNewVolume($s->VolumeLimit);
            //---
            $s->MarginFlags = $symbol->MarginFlags == 'default' ? MTConGroupSymbol::GetDefault('marginflags') : (int) $symbol->MarginFlags;
            $s->MarginInitial = $symbol->MarginInitial == 'default' ? MTConGroupSymbol::GetDefault('margininitial') : (float) $symbol->MarginInitial;
            $s->MarginMaintenance = $symbol->MarginMaintenance == 'default' ? MTConGroupSymbol::GetDefault('marginmaintenance') : (float) $symbol->MarginMaintenance;
            //---
            $this->SetMarginRateInitial($s, $symbol);
            $s->MarginRateMaintenance = $this->GetMarginRateMaintenance($symbol);
            //---
            $s->MarginRateLiquidity = $symbol->MarginLiquidity == 'default' ? MTConGroupSymbol::GetDefault('marginrateliquidity') : (float) $symbol->MarginLiquidity;
            $s->MarginHedged = $symbol->MarginHedged == 'default' ? MTConGroupSymbol::GetDefault('marginhedged') : (float) $symbol->MarginHedged;
            $s->MarginRateCurrency = $symbol->MarginCurrency == 'default' ? MTConGroupSymbol::GetDefault('marginratecurrency') : (float) $symbol->MarginCurrency;
            //---
            $s->SwapMode = $symbol->SwapMode == 'default' ? MTConGroupSymbol::GetDefault('swapmode') : (int) $symbol->SwapMode;
            $s->SwapLong = $symbol->SwapLong == 'default' ? MTConGroupSymbol::GetDefault('swaplong') : (float) $symbol->SwapLong;
            $s->SwapShort = $symbol->SwapShort == 'default' ? MTConGroupSymbol::GetDefault('swapshort') : (float) $symbol->SwapShort;
            $s->Swap3Day = $symbol->Swap3Day == 'default' ? MTConGroupSymbol::GetDefault('swap3day') : (int) $symbol->Swap3Day;
            $s->REFlags = $symbol->REFlags == 'default' ? MTConGroupSymbol::GetDefault('reflags') : (int) $symbol->REFlags;
            $s->RETimeout = $symbol->RETimeout == 'default' ? MTConGroupSymbol::GetDefault('retimeout') : (int) $symbol->RETimeout;
            $s->IEFlags = $symbol->IEFlags == 'default' ? MTConGroupSymbol::GetDefault('ieflags') : (int) $symbol->IEFlags;
            $s->IECheckMode = $symbol->IECheckMode == 'default' ? MTConGroupSymbol::GetDefault('iecheckmode') : (int) $symbol->IECheckMode;
            $s->IETimeout = $symbol->IETimeout == 'default' ? MTConGroupSymbol::GetDefault('ietimeout') : (int) $symbol->IETimeout;
            $s->IESlipProfit = $symbol->IESlipProfit == 'default' ? MTConGroupSymbol::GetDefault('ieslipprofit') : (int) $symbol->IESlipProfit;
            $s->IESlipLosing = $symbol->IESlipLosing == 'default' ? MTConGroupSymbol::GetDefault('iesliplosing') : (int) $symbol->IESlipLosing;
            //---
            $s->IEVolumeMax = $symbol->IEVolumeMax == 'default' ? MTConGroupSymbol::GetDefault('ievolumemax') : (int) $symbol->IEVolumeMax;
            if (isset($symbol->IEVolumeMaxExt))
                $s->IEVolumeMaxExt = $symbol->IEVolumeMaxExt == 'default' ? MTConGroupSymbol::GetDefault('ievolumemax') : (int) $symbol->IEVolumeMaxExt;
            else
                $s->IEVolumeMaxExt = $s->IEVolumeMax == MTConGroupSymbol::GetDefault('ievolumemax') ? $s->IEVolumeMax : MTUtils::ToNewVolume($s->IEVolumeMax);
            //---
            if (isset($symbol->PermissionsFlags))
                $s->PermissionsFlags = (int) $symbol->PermissionsFlags;
            else
                $s->PermissionsFlags = MTEnGroupSymbolPermissions::PERMISSION_BOOK;
            if (isset($symbol->PermissionsBookdepth))
                $s->BookDepthLimit = (int) $symbol->PermissionsBookdepth;
            else
                $s->BookDepthLimit = 0;
            //---
            $result->Symbols[] = $s;
        }
        $obj = null;
        //---
        return $result;
    }

    /**
     * get data for MarginRateInitial
     *
     * @param $obj
     *
     * @return array
     */
    private function SetMarginRateInitial(&$symbol, $obj)
    {
        $result = MTConSymbol::GetDefaultMarginRate();
        $new = false;

        if (isset($obj->MarginInitialBuy)) {
            $result[MTEnMarginRateTypes::MARGIN_RATE_BUY] = $obj->MarginInitialBuy;
            $new = true;
        }

        if (isset($obj->MarginInitialSell)) {
            $result[MTEnMarginRateTypes::MARGIN_RATE_SELL] = $obj->MarginInitialSell;
            $new = true;
        }

        if (isset($obj->MarginInitialBuyLimit)) {
            $result[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT] = $obj->MarginInitialBuyLimit;
            $new = true;
        }

        if (isset($obj->MarginInitialSellLimit)) {
            $result[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT] = $obj->MarginInitialSellLimit;
            $new = true;
        }

        if (isset($obj->MarginInitialBuyStop)) {
            $result[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP] = $obj->MarginInitialBuyStop;
            $new = true;
        }

        if (isset($obj->MarginInitialSellStop)) {
            $result[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP] = $obj->MarginInitialSellStop;
            $new = true;
        }

        if (isset($obj->MarginInitialBuyStopLimit)) {
            $result[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT] = $obj->MarginInitialBuyStopLimit;
            $new = true;
        }

        if (isset($obj->MarginInitialSellStopLimit)) {
            $result[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT] = $obj->MarginInitialSellStopLimit;
            $new = true;
        }

        if (!$new)
            $this->OldMarginRateInitialConvert($symbol, $obj);
        else {
            $symbol->MarginRateInitial = $result;
            $this->OldMarginRateInitialSet($symbol, $obj);
        }
    }

    /**
     * convert from deprecated values to actual
     */
    private function OldMarginRateInitialConvert(&$symbol, $obj)
    {
        $result = MTConSymbol::GetDefaultMarginRate();
        $has_limit = false;

        if (isset($obj->MarginLong)) {
            $symbol->MarginLong = (float) $obj->MarginLong;
            $result[MTEnMarginRateTypes::MARGIN_RATE_BUY] = $symbol->MarginLong;
        }

        if (isset($obj->MarginShort)) {
            $symbol->MarginShort = (float) $obj->MarginShort;
            $result[MTEnMarginRateTypes::MARGIN_RATE_SELL] = $symbol->MarginShort;
        }

        if (isset($obj->MarginLimit)) {
            $symbol->MarginLimit = (float) $obj->MarginLimit;

            $result[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT] = $symbol->MarginLimit * $symbol->MarginLong;
            $result[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT] = $symbol->MarginLimit * $symbol->MarginShort;
        }

        if (isset($obj->MarginStop)) {
            $symbol->MarginStop = (float) $obj->MarginStop;

            $result[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP] = $symbol->MarginStop * $symbol->MarginLong;
            $result[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP] = $symbol->MarginStop * $symbol->MarginShort;
        }

        if (isset($obj->MarginStopLimit)) {
            $symbol->MarginStopLimit = (float) $obj->MarginStopLimit;

            $result[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT] = $symbol->MarginStopLimit * $symbol->MarginLong;
            $result[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT] = $symbol->MarginStopLimit * $symbol->MarginShort;
        }

        $symbol->MarginRateInitial = $result;
    }

    /**
     * set deprecated values for compatibility
     */
    private function OldMarginRateInitialSet(&$symbol, $obj)
    {
        $symbol->MarginLong = $symbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY];
        $symbol->MarginShort = $symbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL];

        $marginLimitLong = 0;
        $marginStopLong = 0;
        $marginStopLimitLong = 0;

        $marginLimitShort = 0;
        $marginStopShort = 0;
        $marginStopLimitShort = 0;

        if ($symbol->MarginLong != 0) {
            $marginLimitLong = $symbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT] / $symbol->MarginLong;
            $marginStopLong = $symbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP] / $symbol->MarginLong;
            $marginStopLimitLong = $symbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT] / $symbol->MarginLong;
        }

        if ($symbol->MarginShort != 0) {
            $marginLimitShort = $symbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT] / $symbol->MarginShort;
            $marginStopShort = $symbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP] / $symbol->MarginShort;
            $marginStopLimitShort = $symbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT] / $symbol->MarginShort;
        }

        $symbol->MarginLimit = max($marginLimitLong, $marginLimitShort);
        $symbol->MarginStop = max($marginStopLong, $marginStopShort);
        $symbol->MarginStopLimit = max($marginStopLimitLong, $marginStopLimitShort);
    }

    /**
     * get data for MarginRateMaintenance
     *
     * @param $obj
     *
     * @return array
     */
    private function GetMarginRateMaintenance($obj)
    {
        $result = MTConSymbol::GetDefaultMarginRate();
        //--- set data
        if (isset($obj->MarginInitialBuy))
            $result[MTEnMarginRateTypes::MARGIN_RATE_BUY] = $obj->MarginMaintenanceBuy;
        if (isset($obj->MarginInitialSell))
            $result[MTEnMarginRateTypes::MARGIN_RATE_SELL] = $obj->MarginMaintenanceSell;
        if (isset($obj->MarginInitialBuyLimit))
            $result[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT] = $obj->MarginMaintenanceBuyLimit;
        if (isset($obj->MarginInitialSellLimit))
            $result[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT] = $obj->MarginMaintenanceSellLimit;
        if (isset($obj->MarginInitialBuyStop))
            $result[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP] = $obj->MarginMaintenanceBuyStop;
        if (isset($obj->MarginInitialSellStop))
            $result[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP] = $obj->MarginMaintenanceSellStop;
        if (isset($obj->MarginInitialBuyStopLimit))
            $result[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT] = $obj->MarginMaintenanceBuyStopLimit;
        if (isset($obj->MarginInitialSellStopLimit))
            $result[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT] = $obj->MarginMaintenanceSellStopLimit;
        //---
        return $result;
    }
}
