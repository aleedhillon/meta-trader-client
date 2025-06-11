<?php

namespace Aleedhillon\MetaTraderClient\Lib;

//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2000-2021, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Class for send request group_total, group_next, group_get
 */
class MTGroupProtocol
{
    /**
     * connection to MetaTrader5 server
     * @var MTConnect
     */
    private $connection;

    //---
    public function __construct($connect)
    {
        $this->connection = $connect;
    }

    /**
     * Get total group
     *
     * @param int $total
     *
     * @return int
     */
    public function GroupTotal(&$total)
    {
        //--- send request
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_GROUP_TOTAL, "")) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseGroupTotal($answer, $group)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //---
        $total = $group->Total;
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Check answer from MetaTrader 5 server
     *
     * @param  string             $answer server answer
     * @param  MTGroupTotalAnswer $groupAnswer
     *
     * @return int
     */
    private function ParseGroupTotal(&$answer, &$groupAnswer)
    {
        $pos = 0;
        //--- get command answer
        $command = $this->connection->GetCommand($answer, $pos);
        if ($command != MTProtocolConsts::WEB_CMD_GROUP_TOTAL)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $groupAnswer = new MTGroupTotalAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $groupAnswer->RetCode = $param['value'];
                    break;
                case MTProtocolConsts::WEB_PARAM_TOTAL:
                    $groupAnswer->Total = (int) $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($groupAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Get group config
     *
     * @param int        $pos - from 0 to total
     * @param MTConGroup $groupNext
     *
     * @return int
     */
    public function GroupNext($pos, &$groupNext)
    {
        $pos = (int) $pos;
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_INDEX => $pos);
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_GROUP_NEXT, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseGroup(MTProtocolConsts::WEB_CMD_GROUP_NEXT, $answer, $groupAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //--- get object from json
        $groupNext = $groupAnswer->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * check answer from MetaTrader 5 server
     *
     * @param string         $command - command
     * @param  string        $answer  - answer from server
     * @param  MTGroupAnswer $groupAnswer
     *
     * @return int
     */
    private function ParseGroup($command, &$answer, &$groupAnswer)
    {
        $pos = 0;
        //--- get command answer
        $commandReal = $this->connection->GetCommand($answer, $pos);
        if ($commandReal != $command)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $groupAnswer = new MTGroupAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $groupAnswer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($groupAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //--- get json
        if (($groupAnswer->ConfigJson = $this->connection->GetJson($answer, $pos)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Add symbol
     *
     * @param MTConGroup $group
     * @param MTConGroup $newGroup
     *
     * @return int
     */
    public function GroupAdd($group, &$newGroup)
    {
        $data = array(MTProtocolConsts::WEB_PARAM_BODYTEXT => $this->GetParams($group));
        //--- send request
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_GROUP_ADD, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseGroup(MTProtocolConsts::WEB_CMD_GROUP_ADD, $answer, $groupAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //--- get object from json
        $newGroup = $groupAnswer->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Get params for send group
     *
     * @param MTConGroup $group - group information
     *
     * @return string - json
     */
    private function GetParams(?MTConGroup $group): string
    {
        if ($group === null || !isset($group->Symbols) || empty($group->Symbols)) {
            return json_encode($group);
        }

        if (!empty($group->Symbols)) {
            foreach ($group->Symbols as &$groupSymbol) {
                if ($groupSymbol === null || !($groupSymbol instanceof MTConGroupSymbol)) continue;

                if ($groupSymbol->TradeMode == MTConGroupSymbol::DEFAULT_VALUE_UINT)
                    $groupSymbol->TradeMode = 'default';
                if ($groupSymbol->ExecMode == MTConGroupSymbol::DEFAULT_VALUE_UINT)
                    $groupSymbol->ExecMode = 'default';
                if ($groupSymbol->FillFlags == MTConGroupSymbol::DEFAULT_VALUE_UINT)
                    $groupSymbol->FillFlags = 'default';
                if ($groupSymbol->ExpirFlags == MTConGroupSymbol::DEFAULT_VALUE_UINT)
                    $groupSymbol->ExpirFlags = 'default';
                if ($groupSymbol->OrderFlags == MTEnOrderFlags::ORDER_FLAGS_NONE)
                    $groupSymbol->OrderFlags = 'default';
                //---
                if ($groupSymbol->SpreadDiff == MTConGroupSymbol::DEFAULT_VALUE_INT)
                    $groupSymbol->SpreadDiff = 'default';
                if ($groupSymbol->SpreadDiffBalance == MTConGroupSymbol::DEFAULT_VALUE_INT)
                    $groupSymbol->SpreadDiffBalance = 'default';
                if ($groupSymbol->StopsLevel == MTConGroupSymbol::DEFAULT_VALUE_INT)
                    $groupSymbol->StopsLevel = 'default';
                if ($groupSymbol->FreezeLevel == MTConGroupSymbol::DEFAULT_VALUE_INT)
                    $groupSymbol->FreezeLevel = 'default';
                //---
                if ($groupSymbol->VolumeMin == MTConGroupSymbol::DEFAULT_VALUE_UINT64)
                    $groupSymbol->VolumeMin = 'default';
                if ($groupSymbol->VolumeMinExt == MTConGroupSymbol::DEFAULT_VALUE_UINT64)
                    $groupSymbol->VolumeMinExt = 'default';
                if ($groupSymbol->VolumeMax == MTConGroupSymbol::DEFAULT_VALUE_UINT64)
                    $groupSymbol->VolumeMax = 'default';
                if ($groupSymbol->VolumeMaxExt == MTConGroupSymbol::DEFAULT_VALUE_UINT64)
                    $groupSymbol->VolumeMaxExt = 'default';
                if ($groupSymbol->VolumeStep == MTConGroupSymbol::DEFAULT_VALUE_UINT64)
                    $groupSymbol->VolumeStep = 'default';
                if ($groupSymbol->VolumeStepExt == MTConGroupSymbol::DEFAULT_VALUE_UINT64)
                    $groupSymbol->VolumeStepExt = 'default';
                if ($groupSymbol->VolumeLimit == MTConGroupSymbol::DEFAULT_VALUE_UINT64)
                    $groupSymbol->VolumeLimit = 'default';
                if ($groupSymbol->VolumeLimitExt == MTConGroupSymbol::DEFAULT_VALUE_UINT64)
                    $groupSymbol->VolumeLimitExt = 'default';
                //---
                if ($groupSymbol->MarginFlags == MTConGroupSymbol::DEFAULT_VALUE_UINT)
                    $groupSymbol->MarginFlags = 'default';
                if ($groupSymbol->MarginInitial == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
                    $groupSymbol->MarginInitial = 'default';
                if ($groupSymbol->MarginMaintenance == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
                    $groupSymbol->MarginMaintenance = 'default';
                //---
                $this->GetMarginRateInitialForJson($groupSymbol);
                $this->GetMarginRateMaintenanceForJson($groupSymbol);
                //--- DEPRECATED
                if ($groupSymbol->MarginLong == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
                    $groupSymbol->MarginLong = 'default';
                if ($groupSymbol->MarginShort == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
                    $groupSymbol->MarginShort = 'default';
                if ($groupSymbol->MarginLimit == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
                    $groupSymbol->MarginLimit = 'default';
                if ($groupSymbol->MarginStop == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
                    $groupSymbol->MarginStop = 'default';
                if ($groupSymbol->MarginStopLimit == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
                    $groupSymbol->MarginStopLimit = 'default';
                //---
                if ($groupSymbol->MarginRateLiquidity == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
                    $groupSymbol->MarginRateLiquidity = 'default';
                $groupSymbol->MarginLiquidity = $groupSymbol->MarginRateLiquidity;

                if ($groupSymbol->MarginHedged == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
                    $groupSymbol->MarginHedged = 'default';
                if ($groupSymbol->MarginRateCurrency == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
                    $groupSymbol->MarginRateCurrency = 'default';
                $groupSymbol->MarginCurrency = $groupSymbol->MarginRateCurrency;
                //---
                if ($groupSymbol->SwapMode == MTConGroupSymbol::DEFAULT_VALUE_UINT)
                    $groupSymbol->SwapMode = 'default';
                if ($groupSymbol->SwapLong == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
                    $groupSymbol->SwapLong = 'default';
                if ($groupSymbol->SwapShort == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
                    $groupSymbol->SwapShort = 'default';
                if ($groupSymbol->Swap3Day == MTConGroupSymbol::DEFAULT_VALUE_INT)
                    $groupSymbol->Swap3Day = 'default';
                //---
                if ($groupSymbol->RETimeout == MTConGroupSymbol::DEFAULT_VALUE_UINT)
                    $groupSymbol->RETimeout = 'default';
                if ($groupSymbol->IEFlags == MTConGroupSymbol::DEFAULT_VALUE_UINT)
                    $groupSymbol->IEFlags = 'default';
                if ($groupSymbol->IECheckMode == MTConGroupSymbol::DEFAULT_VALUE_UINT)
                    $groupSymbol->IECheckMode = 'default';
                if ($groupSymbol->IETimeout == MTConGroupSymbol::DEFAULT_VALUE_UINT)
                    $groupSymbol->IETimeout = 'default';
                if ($groupSymbol->IESlipProfit == MTConGroupSymbol::DEFAULT_VALUE_UINT)
                    $groupSymbol->IESlipProfit = 'default';
                if ($groupSymbol->IESlipLosing == MTConGroupSymbol::DEFAULT_VALUE_UINT)
                    $groupSymbol->IESlipLosing = 'default';

                if ($groupSymbol->IEVolumeMax == MTConGroupSymbol::DEFAULT_VALUE_UINT64)
                    $groupSymbol->IEVolumeMax = 'default';
                if ($groupSymbol->IEVolumeMaxExt == MTConGroupSymbol::DEFAULT_VALUE_UINT64)
                    $groupSymbol->IEVolumeMaxExt = 'default';
                //--- remap BookDepthLimit to PermissionsBookdepth
                $groupSymbol->PermissionsBookdepth = $groupSymbol->BookDepthLimit;
            }
        }
        //---
        return json_encode($group);
    }

    /**
     * array MarginRateInitial for json
     *
     * @param MTConGroupSymbol $groupSymbol
     */
    private function GetMarginRateInitialForJson(MTConGroupSymbol &$groupSymbol): void
    {
        //--- set data
        if (!isset($groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY]) || $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $groupSymbol->MarginInitialBuy = "default";
        else
            $groupSymbol->MarginInitialBuy = $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY];
        //---
        if (!isset($groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY]) || $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $groupSymbol->MarginInitialSell = "default";
        else
            $groupSymbol->MarginInitialSell = $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL];
        //---
        if (!isset($groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT]) || $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $groupSymbol->MarginInitialBuyLimit = "default";
        else
            $groupSymbol->MarginInitialBuyLimit = $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT];
        //---
        if (!isset($groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT]) || $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $groupSymbol->MarginInitialSellLimit = "default";
        else
            $groupSymbol->MarginInitialSellLimit = $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT];
        //---
        if (!isset($groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP]) || $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $groupSymbol->MarginInitialBuyStop = "default";
        else
            $groupSymbol->MarginInitialBuyStop = $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP];
        //---
        if (!isset($groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP]) || $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $groupSymbol->MarginInitialSellStop = "default";
        else
            $groupSymbol->MarginInitialSellStop = $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP];
        //---
        if (!isset($groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT]) || $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $groupSymbol->MarginInitialBuyStopLimit = "default";
        else
            $groupSymbol->MarginInitialBuyStopLimit = $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT];
        //---
        if (!isset($groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT]) || $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $groupSymbol->MarginInitialSellStopLimit = "default";
        else
            $groupSymbol->MarginInitialSellStopLimit = $groupSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT];
    }

    /**
     * array MarginRateInitial for json
     *
     * @param MTConGroupSymbol $groupSymbol
     */
    private function GetMarginRateMaintenanceForJson(MTConGroupSymbol &$groupSymbol): void
    {
        $result = MTConSymbol::GetDefaultMarginRate();
        //--- set data
        if (!isset($groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY]) || $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $groupSymbol->MarginMaintenanceBuy = "default";
        else
            $groupSymbol->MarginMaintenanceBuy = $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY];
        //---
        if (!isset($groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL]) || $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $groupSymbol->MarginMaintenanceSell = "default";
        else
            $groupSymbol->MarginMaintenanceSell = $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL];
        //---
        if (!isset($groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT]) || $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $groupSymbol->MarginMaintenanceBuyLimit = "default";
        else
            $groupSymbol->MarginMaintenanceBuyLimit = $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT];
        //---
        if (!isset($groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT]) || $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $groupSymbol->MarginMaintenanceSellLimit = "default";
        else
            $groupSymbol->MarginMaintenanceSellLimit = $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT];
        //---
        if (!isset($groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP]) || $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $groupSymbol->MarginMaintenanceBuyStop = "default";
        else
            $groupSymbol->MarginMaintenanceBuyStop = $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP];
        //---
        if (!isset($groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP]) || $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $groupSymbol->MarginMaintenanceSellStop = "default";
        else
            $groupSymbol->MarginMaintenanceSellStop = $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP];
        //---
        if (!isset($groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT]) || $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $groupSymbol->MarginMaintenanceBuyStopLimit = "default";
        else
            $groupSymbol->MarginMaintenanceBuyStopLimit = $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT];
        //---
        if (!isset($groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT]) || $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $groupSymbol->MarginMaintenanceSellStopLimit = "default";
        else
            $groupSymbol->MarginMaintenanceSellStopLimit = $groupSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT];
    }

    /**
     * Get information about group by name
     *
     * @param string     $name - group name
     * @param MTConGroup $group
     *
     * @return int
     */
    public function GroupGet($name, &$group)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_GROUP => $name);
        //--- send request
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_GROUP_GET, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseGroup(MTProtocolConsts::WEB_CMD_GROUP_GET, $answer, $groupAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //--- get object from json
        $group = $groupAnswer->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Delete symbol
     *
     * @param string $name
     *
     * @return int
     */
    public function GroupDelete($name)
    {
        $data = array(MTProtocolConsts::WEB_PARAM_GROUP => $name);
        //--- send request
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_GROUP_DELETE, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseClearCommand(MTProtocolConsts::WEB_CMD_GROUP_DELETE, $answer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Check answer from MetaTrader 5 server
     *
     * @param  $command string command
     * @param  $answer  string answer from server
     *
     * @return int
     */
    private function ParseClearCommand($command, &$answer)
    {
        $pos = 0;
        //--- get command answer
        $commandReal = $this->connection->GetCommand($answer, $pos);
        if ($commandReal != $command)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $userAnswer = new MTGroupAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $userAnswer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($userAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //---
        return MTRetCode::MT_RET_OK;
    }
}
