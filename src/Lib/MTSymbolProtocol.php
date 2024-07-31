<?php
namespace Aleedhillon\MetaTraderClient\Lib;

//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2000-2021, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
class MTSymbolProtocol
{
    private $m_connect; // connection to MT5 server
    /**
     * @param MTConnect $connect - connect to MT5 server
     */
    public function __construct($connect)
    {
        $this->m_connect = $connect;
    }

    /**
     * Get total symbols
     *
     * @param int $total - total symbols
     *
     * @return MTRetCode
     */
    public function SymbolTotal(&$total)
    {
        //--- send request
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_SYMBOL_TOTAL, null)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send symbol total failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer symbol total is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseSymbolTotal($answer, $group)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse symbol total failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //---
        $total = $group->Total;
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Check answer from MetaTrader 5 server
     *
     * @param  $answer        string server answer
     * @param  $symbol_answer MTSymbolTotalAnswer
     *
     * @return false
     */
    private function ParseSymbolTotal(&$answer, &$symbol_answer)
    {
        $pos = 0;
        //--- get command answer
        $command = $this->m_connect->GetCommand($answer, $pos);
        if ($command != MTProtocolConsts::WEB_CMD_SYMBOL_TOTAL)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $symbol_answer = new MTSymbolTotalAnswer();
        //--- get param
        $pos_end = -1;
        while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $symbol_answer->RetCode = $param['value'];
                    break;
                case MTProtocolConsts::WEB_PARAM_TOTAL:
                    $symbol_answer->Total = (int) $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($ret_code = MTConnect::GetRetCode($symbol_answer->RetCode)) != MTRetCode::MT_RET_OK)
            return $ret_code;
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Get symbol config
     *
     * @param $pos         int from 0 to total
     * @param $symbol_next MTConSymbol
     *
     * @return MTRetCode
     */
    public function SymbolNext($pos, &$symbol_next)
    {
        $pos = (int) $pos;
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_INDEX => $pos);
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_SYMBOL_NEXT, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send symbol next failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer symbol next is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseSymbol(MTProtocolConsts::WEB_CMD_SYMBOL_NEXT, $answer, $symbol_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse symbol next failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //--- get object from json
        $symbol_next = $symbol_answer->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * check answer from MetaTrader 5 server
     *
     * @param  $command       string command
     * @param  $answer        string answer from server
     * @param  $symbol_answer MTSymbolAnswer
     *
     * @return MTRetCode
     */
    private function ParseSymbol($command, &$answer, &$symbol_answer)
    {
        $pos = 0;
        //--- get command answer
        $command_real = $this->m_connect->GetCommand($answer, $pos);
        if ($command_real != $command)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $symbol_answer = new MTSymbolAnswer();
        //--- get param
        $pos_end = -1;
        while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $symbol_answer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($ret_code = MTConnect::GetRetCode($symbol_answer->RetCode)) != MTRetCode::MT_RET_OK)
            return $ret_code;
        //--- get json
        if (($symbol_answer->ConfigJson = $this->m_connect->GetJson($answer, $pos_end)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Get symbol config
     *
     * @param $name   string - symbol name
     * @param $symbol MTConSymbol
     *
     * @return MTRetCode
     */
    public function SymbolGet($name, &$symbol)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_SYMBOL => $name);
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_SYMBOL_GET, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send symbol get failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer symbol get is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseSymbol(MTProtocolConsts::WEB_CMD_SYMBOL_GET, $answer, $symbol_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse symbol get failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //--- get object from json
        $symbol = $symbol_answer->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Get symbol config
     *
     * @param $name   string - symbol name
     * @param $group  string - group name
     * @param $symbol MTConSymbol
     *
     * @return MTRetCode
     */
    public function SymbolGetGroup($name, $group, &$symbol)
    {
        $data = array(
            MTProtocolConsts::WEB_PARAM_SYMBOL => $name,
            MTProtocolConsts::WEB_PARAM_GROUP => $group
        );
        //--- send request
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_SYMBOL_GET_GROUP, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send symbol get group failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer symbol get group is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseSymbol(MTProtocolConsts::WEB_CMD_SYMBOL_GET_GROUP, $answer, $symbol_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse symbol get group failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //--- get object from json
        $symbol = $symbol_answer->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Add symbol
     *
     * @param MTConSymbol $symbol
     * @param MTConSymbol $new_symbol
     *
     * @return MTRetCode
     */
    public function SymbolAdd($symbol, &$new_symbol)
    {
        $data = array(MTProtocolConsts::WEB_PARAM_BODYTEXT => $this->GetSymbolParams($symbol));
        //--- send request
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_SYMBOL_ADD, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send symbol add failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer symbol add is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseSymbol(MTProtocolConsts::WEB_CMD_SYMBOL_ADD, $answer, $symbol_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse symbol add failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //--- get object from json
        $new_symbol = $symbol_answer->GetFromJson();
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
    public function SymbolDelete($name)
    {
        $data = array(MTProtocolConsts::WEB_PARAM_SYMBOL => $name);
        //--- send request
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_SYMBOL_DELETE, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send symbol delete failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer symbol delete is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseClearCommand(MTProtocolConsts::WEB_CMD_SYMBOL_DELETE, $answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse symbol delete failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
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
        $user_answer = new MTSymbolAnswer();
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

    /**
     * Get params for send symbol
     *
     * @param MTConSymbol $obj - symbol information
     *
     * @return string - json
     */
    private function GetSymbolParams($obj)
    {
        if (isset($obj->MarginRateInitial))
            $this->GetMarginRateInitialForJson($obj);
        if (isset($obj->MarginRateMaintenance))
            $this->GetMarginRateMaintenanceForJson($obj);
        //---
        unset($obj->MarginRateInitial);
        unset($obj->MarginRateMaintenance);
        //--- re-map to real json name
        if (isset($obj->MarginRateLiquidity))
            $obj->MarginLiquidity = $obj->MarginRateLiquidity;
        if (isset($obj->MarginRateCurrency))
            $obj->MarginCurrency = $obj->MarginRateCurrency;
        //---
        return MTJson::Encode($obj);
    }

    /**
     * array MarginRateInitial for json

     *
  *@param MTConSymbol $objSymbol
     */
    private function GetMarginRateInitialForJson(&$objSymbol)
    {
        //--- set data
        if (!isset($objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY]) || $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $objSymbol->MarginInitialBuy = "default";
        else
            $objSymbol->MarginInitialBuy = $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY];
        //---
        if (!isset($objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY]) || $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $objSymbol->MarginInitialSell = "default";
        else
            $objSymbol->MarginInitialSell = $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL];
        //---
        if (!isset($objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT]) || $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $objSymbol->MarginInitialBuyLimit = "default";
        else
            $objSymbol->MarginInitialBuyLimit = $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT];
        //---
        if (!isset($objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT]) || $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $objSymbol->MarginInitialSellLimit = "default";
        else
            $objSymbol->MarginInitialSellLimit = $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT];
        //---
        if (!isset($objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP]) || $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $objSymbol->MarginInitialBuyStop = "default";
        else
            $objSymbol->MarginInitialBuyStop = $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP];
        //---
        if (!isset($objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP]) || $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $objSymbol->MarginInitialSellStop = "default";
        else
            $objSymbol->MarginInitialSellStop = $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP];
        //---
        if (!isset($objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT]) || $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $objSymbol->MarginInitialBuyStopLimit = "default";
        else
            $objSymbol->MarginInitialBuyStopLimit = $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT];
        //---
        if (!isset($objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT]) || $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $objSymbol->MarginInitialSellStopLimit = "default";
        else
            $objSymbol->MarginInitialSellStopLimit = $objSymbol->MarginRateInitial[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT];
    }

    /**
     * array MarginRateInitial for json
     *
     * @param MTConSymbol $objSymbol
     */
    private function GetMarginRateMaintenanceForJson(&$objSymbol)
    {
        //--- set data
        if (!isset($objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY]) || $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $objSymbol->MarginMaintenanceBuy = "default";
        else
            $objSymbol->MarginMaintenanceBuy = $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY];
        //---
        if (!isset($objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL]) || $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $objSymbol->MarginMaintenanceSell = "default";
        else
            $objSymbol->MarginMaintenanceSell = $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL];
        //---
        if (!isset($objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT]) || $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $objSymbol->MarginMaintenanceBuyLimit = "default";
        else
            $objSymbol->MarginMaintenanceBuyLimit = $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_LIMIT];
        //---
        if (!isset($objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT]) || $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $objSymbol->MarginMaintenanceSellLimit = "default";
        else
            $objSymbol->MarginMaintenanceSellLimit = $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_LIMIT];
        //---
        if (!isset($objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP]) || $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $objSymbol->MarginMaintenanceBuyStop = "default";
        else
            $objSymbol->MarginMaintenanceBuyStop = $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP];
        //---
        if (!isset($objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP]) || $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $objSymbol->MarginMaintenanceSellStop = "default";
        else
            $objSymbol->MarginMaintenanceSellStop = $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP];
        //---
        if (!isset($objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT]) || $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $objSymbol->MarginMaintenanceBuyStopLimit = "default";
        else
            $objSymbol->MarginMaintenanceBuyStopLimit = $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_BUY_STOP_LIMIT];
        //---
        if (!isset($objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT]) || $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT] == MTConGroupSymbol::DEFAULT_VALUE_DOUBLE)
            $objSymbol->MarginMaintenanceSellStopLimit = "default";
        else
            $objSymbol->MarginMaintenanceSellStopLimit = $objSymbol->MarginRateMaintenance[MTEnMarginRateTypes::MARGIN_RATE_SELL_STOP_LIMIT];
    }
}
