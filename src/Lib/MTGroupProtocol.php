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
    private $m_connect;

    //---
    public function __construct($connect)
    {
        $this->m_connect = $connect;
    }

    /**
     * Get total group
     *
     * @param int $total
     *
     * @return MTRetCode
     */
    public function GroupTotal(&$total)
    {
        //--- send request
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_GROUP_TOTAL, "")) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send group total failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer group total is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseGroupTotal($answer, $group)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse group total failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
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
     * @param  MTGroupTotalAnswer $group_answer
     *
     * @return MTRetCode
     */
    private function ParseGroupTotal(&$answer, &$group_answer)
    {
        $pos = 0;
        //--- get command answer
        $command = $this->m_connect->GetCommand($answer, $pos);
        if ($command != MTProtocolConsts::WEB_CMD_GROUP_TOTAL)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $group_answer = new MTGroupTotalAnswer();
        //--- get param
        $pos_end = -1;
        while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $group_answer->RetCode = $param['value'];
                    break;
                case MTProtocolConsts::WEB_PARAM_TOTAL:
                    $group_answer->Total = (int) $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($ret_code = MTConnect::GetRetCode($group_answer->RetCode)) != MTRetCode::MT_RET_OK)
            return $ret_code;
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Get group config
     *
     * @param int        $pos - from 0 to total
     * @param MTConGroup $group_next
     *
     * @return MTRetCode
     */
    public function GroupNext($pos, &$group_next)
    {
        $pos = (int) $pos;
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_INDEX => $pos);
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_GROUP_NEXT, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send group next failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer group next is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseGroup(MTProtocolConsts::WEB_CMD_GROUP_NEXT, $answer, $group_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse group next failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //--- get object from json
        $group_next = $group_answer->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * check answer from MetaTrader 5 server
     *
     * @param string         $command - command
     * @param  string        $answer  - answer from server
     * @param  MTGroupAnswer $group_answer
     *
     * @return MTRetCode
     */
    private function ParseGroup($command, &$answer, &$group_answer)
    {
        $pos = 0;
        //--- get command answer
        $command_real = $this->m_connect->GetCommand($answer, $pos);
        if ($command_real != $command)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $group_answer = new MTGroupAnswer();
        //--- get param
        $pos_end = -1;
        while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $group_answer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($ret_code = MTConnect::GetRetCode($group_answer->RetCode)) != MTRetCode::MT_RET_OK)
            return $ret_code;
        //--- get json
        if (($group_answer->ConfigJson = $this->m_connect->GetJson($answer, $pos)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Add symbol
     *
     * @param MTConGroup $group
     * @param MTConGroup $new_group
     *
     * @return MTRetCode
     */
    public function GroupAdd($group, &$new_group)
    {
        $data = array(MTProtocolConsts::WEB_PARAM_BODYTEXT => $this->GetParams($group));
        //--- send request
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_GROUP_ADD, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send group add failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer group add is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseGroup(MTProtocolConsts::WEB_CMD_GROUP_ADD, $answer, $group_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse group add failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //--- get object from json
        $new_group = $group_answer->GetFromJson();
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
    private function GetParams($group)
    {
        if (!empty($group->Symbols)) {
            foreach ($group->Symbols as &$groupSymbol) {
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
    private function GetMarginRateInitialForJson(&$groupSymbol)
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
    private function GetMarginRateMaintenanceForJson(&$groupSymbol)
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
     * @return MTRetCode
     */
    public function GroupGet($name, &$group)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_GROUP => $name);
        //--- send request
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_GROUP_GET, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send group get by name ' . $name . ' failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer group get is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseGroup(MTProtocolConsts::WEB_CMD_GROUP_GET, $answer, $group_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse group get failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //--- get object from json
        $group = $group_answer->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Delete symbol
     *
     * @param string $name
     *
     * @return MTRetCode
     */
    public function GroupDelete($name)
    {
        $data = array(MTProtocolConsts::WEB_PARAM_GROUP => $name);
        //--- send request
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_GROUP_DELETE, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send group delete failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer group delete is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseClearCommand(MTProtocolConsts::WEB_CMD_GROUP_DELETE, $answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse group delete failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
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
     * @return MTRetCode
     */
    private function ParseClearCommand($command, &$answer)
    {
        $pos = 0;
        //--- get command answer
        $command_real = $this->m_connect->GetCommand($answer, $pos);
        if ($command_real != $command)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $user_answer = new MTGroupAnswer();
        //--- get param
        $pos_end = -1;
        while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $user_answer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($ret_code = MTConnect::GetRetCode($user_answer->RetCode)) != MTRetCode::MT_RET_OK)
            return $ret_code;
        //---
        return MTRetCode::MT_RET_OK;
    }
}
