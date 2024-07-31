<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * Symbols configuration for clients group
 */
class MTConGroupSymbol
{
    const DEFAULT_VALUE_UINT = 0xffffffff;
    const DEFAULT_VALUE_INT = 0x7fffffff;
    const DEFAULT_VALUE_UINT64 = 0xffffffffffffffff;
    const DEFAULT_VALUE_INT64 = 0x7fffffffffffffff;
    const DEFAULT_VALUE_DOUBLE = 1.7976931348623158e+308;
    //--- symbol or symbol groups path
    public $Path;
    //--- MTEnTradeMode
    public $TradeMode;
    //--- MTEnCalcMode
    public $ExecMode;
    //--- MTEnFillingFlags
    public $FillFlags;
    //--- MTEnExpirationFlags
    public $ExpirFlags;
    //--- Flags trade orders (type is MTEnOrderFlags)
    public $OrderFlags;
    //--- spread difference (0 - floating spread)
    public $SpreadDiff;
    //--- spread difference balance
    public $SpreadDiffBalance;
    //--- stops level
    public $StopsLevel;
    //--- freeze level
    public $FreezeLevel;
    //--- minimal volume
    public $VolumeMin;
    //--- minimal volume
    public $VolumeMinExt;
    //--- maximal volume
    public $VolumeMax;
    //--- maximal volume
    public $VolumeMaxExt;
    //--- volume step
    public $VolumeStep;
    //--- volume step
    public $VolumeStepExt;
    //--- cumulative positions and orders limit
    public $VolumeLimit;
    //--- cumulative positions and orders limit
    public $VolumeLimitExt;
    //--- MTEnGroupMarginFlags
    public $MarginFlags;
    //--- initial margin
    public $MarginInitial;
    //--- maintenance margin
    public $MarginMaintenance;
    /**
     * orders and positions margin rates
     * @var array
     */
    public $MarginRateInitial;
    /**
     * orders and positions margin rates
     * @var array
     */
    public $MarginRateMaintenance;
    /**
     * orders and positions margin rates
     * @var double
     */
    public $MarginRateLiquidity;
    /**
     * hedged positions margin rate
     * @var double
     */
    public $MarginHedged;
    /**
     * margin currency rate
     * @var double
     */
    public $MarginRateCurrency;
    /**
     * long orders and positions margin rate
     * @deprecated
     * @var float
     */
    public $MarginLong;
    /**
     * short orders and positions margin rate
     * @deprecated
     * @var float
     */
    public $MarginShort;
    /**
     * limit orders and positions margin rate
     * @deprecated
     * @var float
     */
    public $MarginLimit;
    /**
     * stop orders and positions margin rate
     * @deprecated
     * @var float
     */
    public $MarginStop;
    /**
     * stop-limit orders and positions margin rate
     * @deprecated
     * @var float
     */
    public $MarginStopLimit;
    //--- MTEnSwapMode
    public $SwapMode;
    //--- long positions swaps rate
    public $SwapLong;
    //--- short positions swaps rate
    public $SwapShort;
    //--- 3 time swaps day
    public $Swap3Day;
    //--- request execution flags (type is MTEnREFlags)
    public $REFlags;
    //--- instant execution
    public $RETimeout;
    //---
    public $IEFlags;
    //--- instant execution check mode
    public $IECheckMode;
    //--- instant execution timeout
    public $IETimeout;
    //--- instant execution profit slippage
    public $IESlipProfit;
    //--- instant execution losing slippage
    public $IESlipLosing;
    //--- instant execution max volume
    public $IEVolumeMax;
    //--- instant execution max volume
    public $IEVolumeMaxExt;
    //--- MTEnConSymbolPermissions
    public $PermissionsFlags;
    //--- book depth limit
    public $BookDepthLimit;

    /**
     * Create MTConGroupSymbol with default values
     * @return MTConGroupSymbol
     */
    public static function CreateDefault()
    {
        $groupSymbol = new MTConGroupSymbol();
        //---
        $groupSymbol->TradeMode = MTConGroupSymbol::DEFAULT_VALUE_UINT;
        $groupSymbol->ExecMode = MTConGroupSymbol::DEFAULT_VALUE_UINT;
        $groupSymbol->FillFlags = MTConGroupSymbol::DEFAULT_VALUE_UINT;
        $groupSymbol->ExpirFlags = MTConGroupSymbol::DEFAULT_VALUE_UINT;
        $groupSymbol->OrderFlags = MTEnOrderFlags::ORDER_FLAGS_NONE;
        $groupSymbol->SpreadDiff = MTConGroupSymbol::DEFAULT_VALUE_INT;
        $groupSymbol->SpreadDiffBalance = MTConGroupSymbol::DEFAULT_VALUE_INT;
        $groupSymbol->StopsLevel = MTConGroupSymbol::DEFAULT_VALUE_INT;
        $groupSymbol->FreezeLevel = MTConGroupSymbol::DEFAULT_VALUE_INT;
        $groupSymbol->VolumeMin = MTConGroupSymbol::DEFAULT_VALUE_UINT64;
        $groupSymbol->VolumeMinExt = MTConGroupSymbol::DEFAULT_VALUE_UINT64;
        $groupSymbol->VolumeMax = MTConGroupSymbol::DEFAULT_VALUE_UINT64;
        $groupSymbol->VolumeMaxExt = MTConGroupSymbol::DEFAULT_VALUE_UINT64;
        $groupSymbol->VolumeStep = MTConGroupSymbol::DEFAULT_VALUE_UINT64;
        $groupSymbol->VolumeStepExt = MTConGroupSymbol::DEFAULT_VALUE_UINT64;
        $groupSymbol->VolumeLimit = MTConGroupSymbol::DEFAULT_VALUE_UINT64;
        $groupSymbol->VolumeLimitExt = MTConGroupSymbol::DEFAULT_VALUE_UINT64;
        $groupSymbol->MarginFlags = MTConGroupSymbol::DEFAULT_VALUE_UINT;
        $groupSymbol->MarginInitial = MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
        $groupSymbol->MarginMaintenance = MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
        //---
        $groupSymbol->MarginRateInitial = array(
            MTEnMarginRateTypes::MARGIN_RATE_BUY => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
            MTEnMarginRateTypes::MARGIN_RATE_SELL => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
            MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
            MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
            MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
            MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
            MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
            MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE
        );
        //---
        $groupSymbol->MarginRateMaintenance = array(
            MTEnMarginRateTypes::MARGIN_RATE_BUY => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
            MTEnMarginRateTypes::MARGIN_RATE_SELL => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
            MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
            MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
            MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
            MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
            MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE,
            MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT => MTConGroupSymbol::DEFAULT_VALUE_DOUBLE
        );
        //---
        $groupSymbol->MarginRateLiquidity = MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
        $groupSymbol->MarginHedged = MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
        $groupSymbol->MarginRateCurrency = MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
        //--- DEPRECATED
        $groupSymbol->MarginLong = MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
        $groupSymbol->MarginShort = MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
        $groupSymbol->MarginLimit = MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
        $groupSymbol->MarginStop = MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
        $groupSymbol->MarginStopLimit = MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
        //---
        $groupSymbol->SwapMode = MTConGroupSymbol::DEFAULT_VALUE_UINT;
        $groupSymbol->SwapLong = MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
        $groupSymbol->SwapShort = MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
        $groupSymbol->Swap3Day = MTConGroupSymbol::DEFAULT_VALUE_INT;
        $groupSymbol->REFlags = MTConGroupSymbol::DEFAULT_VALUE_UINT;
        $groupSymbol->RETimeout = MTConGroupSymbol::DEFAULT_VALUE_UINT;
        $groupSymbol->IEFlags = MTConGroupSymbol::DEFAULT_VALUE_UINT;
        $groupSymbol->IECheckMode = MTConGroupSymbol::DEFAULT_VALUE_UINT;
        $groupSymbol->IETimeout = MTConGroupSymbol::DEFAULT_VALUE_UINT;
        $groupSymbol->IESlipProfit = MTConGroupSymbol::DEFAULT_VALUE_UINT;
        $groupSymbol->IESlipLosing = MTConGroupSymbol::DEFAULT_VALUE_UINT;
        $groupSymbol->IEVolumeMax = MTConGroupSymbol::DEFAULT_VALUE_UINT64;
        $groupSymbol->IEVolumeMaxExt = MTConGroupSymbol::DEFAULT_VALUE_UINT64;
        //---
        $groupSymbol->PermissionsFlags = MTEnGroupSymbolPermissions::PERMISSION_DEFAULT;
        $groupSymbol->BookDepthLimit = 0;
        //---
        return $groupSymbol;
    }

    /**
     * Get default value by name
     *
     * @param string $name
     *
     * @return int|uint|float
     */
    public static function GetDefault($name)
    {
        switch (strtolower($name)) {
            case "trademode":
                return MTConGroupSymbol::DEFAULT_VALUE_UINT;
            case "execmode":
                return MTConGroupSymbol::DEFAULT_VALUE_UINT;
            case "fillflags":
                return MTConGroupSymbol::DEFAULT_VALUE_UINT;
            case "expirflags":
                return MTConGroupSymbol::DEFAULT_VALUE_UINT;
            case "orderflags":
                return MTConGroupSymbol::DEFAULT_VALUE_UINT;
            case "spreaddiff":
                return MTConGroupSymbol::DEFAULT_VALUE_INT;
            case "spreaddiffbalance":
                return MTConGroupSymbol::DEFAULT_VALUE_INT;
            case "stopslevel":
                return MTConGroupSymbol::DEFAULT_VALUE_INT;
            case "freezelevel":
                return MTConGroupSymbol::DEFAULT_VALUE_INT;
            case "volumemin":
                return MTConGroupSymbol::DEFAULT_VALUE_UINT64;
            case "volumemax":
                return MTConGroupSymbol::DEFAULT_VALUE_UINT64;
            case "volumestep":
                return MTConGroupSymbol::DEFAULT_VALUE_UINT64;
            case "volumelimit":
                return MTConGroupSymbol::DEFAULT_VALUE_UINT64;
            case "marginflags":
                return MTConGroupSymbol::DEFAULT_VALUE_UINT;
            case "margininitial":
                return MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
            case "marginmaintenance":
                return MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
            case "marginrateliquidity":
                return MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
            case "marginhedged":
                return MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
            case "marginratecurrency":
                return MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
            case "swapmode":
                return MTConGroupSymbol::DEFAULT_VALUE_UINT;
            case "swaplong":
                return MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
            case "swapshort":
                return MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
            case "swap3day":
                return MTConGroupSymbol::DEFAULT_VALUE_INT;
            case "retimeout":
                return MTConGroupSymbol::DEFAULT_VALUE_UINT;
            case "ieflags":
                return MTConGroupSymbol::DEFAULT_VALUE_UINT;
            case "iecheckmode":
                return MTConGroupSymbol::DEFAULT_VALUE_UINT;
            case "ietimeout":
                return MTConGroupSymbol::DEFAULT_VALUE_UINT;
            case "ieslipprofit":
                return MTConGroupSymbol::DEFAULT_VALUE_UINT;
            case "iesliplosing":
                return MTConGroupSymbol::DEFAULT_VALUE_UINT;
            case "ievolumemax":
                return MTConGroupSymbol::DEFAULT_VALUE_UINT64;
            //--- DEPRECATED
            case "marginlong":
                return MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
            case "marginshort":
                return MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
            case "marginlimit":
                return MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
            case "marginstop":
                return MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
            case "marginstoplimit":
                return MTConGroupSymbol::DEFAULT_VALUE_DOUBLE;
        }
        //---
        return MTConGroupSymbol::DEFAULT_VALUE_UINT;
    }
}
