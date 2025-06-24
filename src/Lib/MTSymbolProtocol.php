<?php

namespace Aleedhillon\MetaTraderClient\Lib;

//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2000-2021, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
class MTSymbolProtocol
{
    private $connection; // connection to MT5 server
    /**
     * @param MTConnect $connect - connect to MT5 server
     */
    public function __construct($connect)
    {
        $this->connection = $connect;
    }

    /**
     * Get total symbols
     *
     * @param int $total - total symbols
     *
     * @return int
     */
    public function SymbolTotal(&$total)
    {
        //--- send request
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_SYMBOL_TOTAL, null)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseSymbolTotal($answer, $group)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //---
        $total = $group->Total;
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Check answer from MetaTrader 5 server
     *
     * @param  $answer        string server answer
     * @param  $symbolAnswer MTSymbolTotalAnswer
     *
     * @return int
     */
    private function ParseSymbolTotal(&$answer, &$symbolAnswer)
    {
        $pos = 0;
        //--- get command answer
        $command = $this->connection->GetCommand($answer, $pos);
        if ($command != MTProtocolConsts::WEB_CMD_SYMBOL_TOTAL)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $symbolAnswer = new MTSymbolTotalAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $symbolAnswer->RetCode = $param['value'];
                    break;
                case MTProtocolConsts::WEB_PARAM_TOTAL:
                    $symbolAnswer->Total = (int) $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($symbolAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Get symbol config
     *
     * @param $pos         int from 0 to total
     * @param $symbolNext MTConSymbol
     *
     * @return int
     */
    public function SymbolNext($pos, &$symbolNext)
    {
        $pos = (int) $pos;
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_INDEX => $pos);
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_SYMBOL_NEXT, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseSymbol(MTProtocolConsts::WEB_CMD_SYMBOL_NEXT, $answer, $symbolAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //--- get object from json
        $symbolNext = $symbolAnswer->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * check answer from MetaTrader 5 server
     *
     * @param  $command       string command
     * @param  $answer        string answer from server
     * @param  $symbolAnswer MTSymbolAnswer
     *
     * @return int
     */
    private function ParseSymbol($command, &$answer, &$symbolAnswer)
    {
        $pos = 0;
        //--- get command answer
        $commandReal = $this->connection->GetCommand($answer, $pos);
        if ($commandReal != $command)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $symbolAnswer = new MTSymbolAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $symbolAnswer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($symbolAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //--- get json
        if (($symbolAnswer->ConfigJson = $this->connection->GetJson($answer, $posEnd)) == null)
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
     * @return int
     */
    public function SymbolGet($name, &$symbol)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_SYMBOL => $name);
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_SYMBOL_GET, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseSymbol(MTProtocolConsts::WEB_CMD_SYMBOL_GET, $answer, $symbolAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //--- get object from json
        $symbol = $symbolAnswer->GetFromJson();
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
     * @return int
     */
    public function SymbolGetGroup($name, $group, &$symbol)
    {
        $data = array(
            MTProtocolConsts::WEB_PARAM_SYMBOL => $name,
            MTProtocolConsts::WEB_PARAM_GROUP => $group
        );
        //--- send request
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_SYMBOL_GET_GROUP, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseSymbol(MTProtocolConsts::WEB_CMD_SYMBOL_GET_GROUP, $answer, $symbolAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //--- get object from json
        $symbol = $symbolAnswer->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Add symbol
     *
     * @param MTConSymbol $symbol
     * @param MTConSymbol $newSymbol
     *
     * @return int
     */
    public function SymbolAdd($symbol, &$newSymbol)
    {
        $data = array(MTProtocolConsts::WEB_PARAM_BODYTEXT => $this->GetSymbolParams($symbol));
        //--- send request
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_SYMBOL_ADD, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseSymbol(MTProtocolConsts::WEB_CMD_SYMBOL_ADD, $answer, $symbolAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //--- get object from json
        $newSymbol = $symbolAnswer->GetFromJson();
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
    public function SymbolDelete($name)
    {
        $data = array(MTProtocolConsts::WEB_PARAM_SYMBOL => $name);
        //--- send request
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_SYMBOL_DELETE, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseClearCommand(MTProtocolConsts::WEB_CMD_SYMBOL_DELETE, $answer)) != MTRetCode::MT_RET_OK) {
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
        $symbolAnswer = new MTSymbolAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $symbolAnswer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($symbolAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
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
