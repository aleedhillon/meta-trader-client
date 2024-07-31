<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * get symbol info
 */
class MTSymbolAnswer
{
    public $RetCode = '-1';
    public $ConfigJson = '';

    /**
     * From json get class MTConSymbol
     * @return MTConSymbol
     */
    public function GetFromJson()
    {
        $obj = MTJson::Decode($this->ConfigJson);
        if ($obj == null)
            return null;

        $result = new MTConSymbol();
        //---
        $result->Symbol = (string) $obj->Symbol;
        $result->Path = (string) $obj->Path;
        $result->ISIN = (string) $obj->ISIN;
        $result->Description = (string) $obj->Description;
        $result->International = (string) $obj->International;
        $result->Basis = (string) $obj->Basis;
        $result->Source = (string) $obj->Source;
        $result->Page = (string) $obj->Page;
        $result->CurrencyBase = (string) $obj->CurrencyBase;
        $result->CurrencyBaseDigits = (int) $obj->CurrencyBaseDigits;
        $result->CurrencyProfit = (string) $obj->CurrencyProfit;
        $result->CurrencyProfitDigits = (int) $obj->CurrencyProfitDigits;
        $result->CurrencyMargin = (string) $obj->CurrencyMargin;
        $result->CurrencyMarginDigits = (int) $obj->CurrencyMarginDigits;
        $result->Color = (int) $obj->Color;
        $result->ColorBackground = (int) $obj->ColorBackground;
        $result->Digits = (int) $obj->Digits;
        $result->Point = (float) $obj->Point;
        $result->Multiply = (float) $obj->Multiply;
        $result->TickFlags = (int) $obj->TickFlags;
        $result->TickBookDepth = (int) $obj->TickBookDepth;
        $result->ChartMode = (int) $obj->TickChartMode;
        $result->FilterSoft = (int) $obj->FilterSoft;
        $result->FilterSoftTicks = (int) $obj->FilterSoftTicks;
        $result->FilterHard = (int) $obj->FilterHard;
        $result->FilterHardTicks = (int) $obj->FilterHardTicks;
        $result->FilterDiscard = (int) $obj->FilterDiscard;
        $result->FilterSpreadMax = (int) $obj->FilterSpreadMax;
        $result->FilterSpreadMin = (int) $obj->FilterSpreadMin;
        $result->FilterGap = (int) $obj->FilterGap;
        $result->FilterGapTicks = (int) $obj->FilterGapTicks;
        $result->TradeMode = (int) $obj->TradeMode;
        $result->TradeFlags = (int) $obj->TradeFlags;
        $result->CalcMode = (int) $obj->CalcMode;
        $result->ExecMode = (int) $obj->ExecMode;
        $result->GTCMode = (int) $obj->GTCMode;
        $result->FillFlags = (int) $obj->FillFlags;
        $result->ExpirFlags = (int) $obj->ExpirFlags;
        $result->OrderFlags = (int) $obj->OrderFlags;
        $result->Spread = (int) $obj->Spread;
        $result->SpreadBalance = (int) $obj->SpreadBalance;
        $result->SpreadDiff = (int) $obj->SpreadDiff;
        $result->SpreadDiffBalance = (int) $obj->SpreadDiffBalance;
        $result->TickValue = (float) $obj->TickValue;
        $result->TickSize = (float) $obj->TickSize;
        $result->ContractSize = (float) $obj->ContractSize;
        $result->StopsLevel = (int) $obj->StopsLevel;
        $result->FreezeLevel = (int) $obj->FreezeLevel;
        $result->QuotesTimeout = (int) $obj->QuotesTimeout;
        $result->VolumeMin = (int) $obj->VolumeMin;
        if (isset($obj->VolumeMinExt))
            $result->VolumeMinExt = (int) $obj->VolumeMinExt;
        else
            $result->VolumeMinExt = MTUtils::ToNewVolume($obj->VolumeMin);
        $result->VolumeMax = (int) $obj->VolumeMax;
        if (isset($obj->VolumeMaxExt))
            $result->VolumeMaxExt = (int) $obj->VolumeMaxExt;
        else
            $result->VolumeMaxExt = MTUtils::ToNewVolume($obj->VolumeMax);
        $result->VolumeStep = (int) $obj->VolumeStep;
        if (isset($obj->VolumeStepExt))
            $result->VolumeStepExt = (int) $obj->VolumeStepExt;
        else
            $result->VolumeStepExt = MTUtils::ToNewVolume($obj->VolumeStep);
        $result->VolumeLimit = (int) $obj->VolumeLimit;
        if (isset($obj->VolumeLimitExt))
            $result->VolumeLimitExt = (int) $obj->VolumeLimitExt;
        else
            $result->VolumeLimitExt = MTUtils::ToNewVolume($obj->VolumeLimit);
        //---
        $result->MarginFlags = (int) $obj->MarginFlags;
        $result->MarginInitial = (float) $obj->MarginInitial;
        $result->MarginMaintenance = (float) $obj->MarginMaintenance;
        $result->MarginRateLiquidity = (float) $obj->MarginLiquidity;
        $result->MarginHedged = (float) $obj->MarginHedged;
        $result->MarginRateCurrency = (float) $obj->MarginCurrency;
        //---
        $this->SetMarginRateInitial($result, $obj);
        $result->MarginRateMaintenance = $this->GetMarginRateMaintenance($obj);
        //---
        $result->SwapMode = (int) $obj->SwapMode;
        $result->SwapLong = (float) $obj->SwapLong;
        $result->SwapShort = (float) $obj->SwapShort;
        $result->Swap3Day = (int) $obj->Swap3Day;
        $result->TimeStart = (int) $obj->TimeStart;
        $result->TimeExpiration = (int) $obj->TimeExpiration;
        //--- data of session
        $result->SessionsQuotes = $this->GetSessions($obj->SessionsQuotes);
        $result->SessionsTrades = $this->GetSessions($obj->SessionsTrades);
        //---
        $result->REFlags = (int) $obj->REFlags;
        $result->RETimeout = (int) $obj->RETimeout;
        $result->IECheckMode = (int) $obj->IECheckMode;
        $result->IETimeout = (int) $obj->IETimeout;
        $result->IESlipProfit = (int) $obj->IESlipProfit;
        $result->IESlipLosing = (int) $obj->IESlipLosing;
        $result->IEVolumeMax = (int) $obj->IEVolumeMax;
        if (isset($obj->IEVolumeMaxExt))
            $result->IEVolumeMaxExt = (int) $obj->IEVolumeMaxExt;
        else
            $result->IEVolumeMaxExt = MTUtils::ToNewVolume($obj->IEVolumeMax);
        //---
        $result->PriceSettle = (float) $obj->PriceSettle;
        $result->PriceLimitMax = (float) $obj->PriceLimitMax;
        $result->PriceLimitMin = (float) $obj->PriceLimitMin;
        $result->PriceStrike = (float) $obj->PriceStrike;
        //--- support both name old or new
        if (isset($obj->OptionMode))
            $result->OptionsMode = (int) $obj->OptionMode;
        else if (isset($obj->OptionsMode))
            $result->OptionsMode = (int) $obj->OptionsMode;
        //---
        $result->FaceValue = (float) $obj->FaceValue;
        $result->AccruedInterest = (float) $obj->AccruedInterest;
        $result->SpliceType = (int) $obj->SpliceType;
        $result->SpliceTimeType = (int) $obj->SpliceTimeType;
        $result->SpliceTimeDays = (int) $obj->SpliceTimeDays;
        //---
        if (isset($obj->IEFlags))
            $result->IEFlags = (int) $obj->IEFlags;
        else
            $result->IEFlags = 0;
        //---
        if (isset($obj->Category))
            $result->Category = (string) $obj->Category;
        else
            $result->Category = "";
        //---
        if (isset($obj->Exchange))
            $result->Exchange = (string) $obj->Exchange;
        else
            $result->Exchange = "";
        //---
        if (isset($obj->CFI))
            $result->CFI = (string) $obj->CFI;
        else
            $result->CFI = "";
        //---
        if (isset($obj->Sector))
            $result->Sector = (int) $obj->Sector;
        else
            $result->Sector = 0;
        //---
        if (isset($obj->Industry))
            $result->Industry = (int) $obj->Industry;
        else
            $result->Industry = 0;
        //---
        if (isset($obj->Country))
            $result->Country = (string) $obj->Country;
        else
            $result->Country = "";
        //---
        if (isset($obj->SubscriptionsDelay))
            $result->SubscriptionsDelay = (int) $obj->SubscriptionsDelay;
        else
            $result->SubscriptionsDelay = 0;
        //---
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
        if (isset($obj->MarginMaintenanceBuy))
            $result[MTEnMarginRateTypes::MARGIN_RATE_BUY] = $obj->MarginMaintenanceBuy;
        if (isset($obj->MarginMaintenanceSell))
            $result[MTEnMarginRateTypes::MARGIN_RATE_SELL] = $obj->MarginMaintenanceSell;
        if (isset($obj->MarginMaintenanceBuyLimit))
            $result[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT] = $obj->MarginMaintenanceBuyLimit;
        if (isset($obj->MarginMaintenanceSellLimit))
            $result[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT] = $obj->MarginMaintenanceSellLimit;
        if (isset($obj->MarginMaintenanceBuyStop))
            $result[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP] = $obj->MarginMaintenanceBuyStop;
        if (isset($obj->MarginMaintenanceSellStop))
            $result[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP] = $obj->MarginMaintenanceSellStop;
        if (isset($obj->MarginMaintenanceBuyStopLimit))
            $result[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT] = $obj->MarginMaintenanceBuyStopLimit;
        if (isset($obj->MarginMaintenanceSellStopLimit))
            $result[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT] = $obj->MarginMaintenanceSellStopLimit;
        //---
        return $result;
    }

    /**
     * @param $list array - info about session
     *
     * @return array|null
     */
    private function GetSessions($list)
    {
        if (empty($list))
            return null;
        $result = array();
        //---
        $i = 0;
        foreach ($list as $sessions) {
            if (empty($sessions) || empty($sessions[0])) {
                $result[$i] = null;
                $i++;
                continue;
            }
            //---
            $result[$i] = array();
            //---
            foreach ($sessions as $session) {
                //---
                $sess = new MTConSymbolSession();
                $sess->Open = $session->Open;
                $sess->Close = $session->Close;
                //---
                $result[$i][] = $sess;
            }
            //---
            $i++;
        }
        return $result;
    }
}
