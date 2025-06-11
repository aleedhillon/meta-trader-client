<?php

namespace Aleedhillon\MetaTraderClient\Lib;

//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2000-2021, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Work with tick
 */
class MTTickProtocol
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
     * Get last ticks
     * @param string $symbol - name symbol
     * @param array(MTTick) $ticks
     * @return int
     */
    public function TickLast($symbol, &$ticks)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_SYMBOL => $symbol);
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_TICK_LAST, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        $tickAnswer = null;

        if (($errorCode = $this->Parse(MTProtocolConsts::WEB_CMD_TICK_LAST, $answer, $tickAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //--- get object from json
        $ticks = $tickAnswer->GetArrayFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * check answer from MetaTrader 5 server
     * @param string $command - command
     * @param string $answer - answer from server
     * @param  MTTickAnswer $tickAnswer
     * @return int
     */
    private function Parse($command, &$answer, &$tickAnswer)
    {
        $pos = 0;
        //--- get command answer
        $commandReal = $this->connection->GetCommand($answer, $pos);
        if ($commandReal != $command)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $tickAnswer = new MTTickAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $tickAnswer->RetCode = $param['value'];
                    break;
                case MTProtocolConsts::WEB_PARAM_TRANS_ID:
                    $tickAnswer->TransId = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($tickAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //--- get json
        if (($tickAnswer->ConfigJson = $this->connection->GetJson($answer, $posEnd)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Get last tickets by symbol and group
     * @param string $symbol
     * @param string $group
     * @param array(MTTick) $ticks
     * @return int
     */
    public function TickLastGroup($symbol, $group, &$ticks)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_SYMBOL => $symbol, MTProtocolConsts::WEB_PARAM_GROUP => $group);
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_TICK_LAST_GROUP, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        $tickAnswer = null;
        //---
        if (($errorCode = $this->Parse(MTProtocolConsts::WEB_CMD_TICK_LAST_GROUP, $answer, $tickAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //--- get object from json
        $ticks = $tickAnswer->GetArrayFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Get stat ticks
     * @param string $symbol - name symbol
     * @param array(MTTickStat) $tickStat
     * @return int
     */
    public function TickStat($symbol, &$tickStat)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_SYMBOL => $symbol);
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_TICK_STAT, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        $tickAnswer = null;

        if (($errorCode = $this->ParseTickStat(MTProtocolConsts::WEB_CMD_TICK_STAT, $answer, $tickAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //--- get object from json
        $tickStat = $tickAnswer->GetArrayFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * check answer from MetaTrader 5 server
     * @param string $command - command
     * @param string $answer - answer from server
     * @param  MTTickAnswer $tickAnswer
     * @return int
     */
    private function ParseTickStat($command, &$answer, &$tickAnswer)
    {
        $pos = 0;
        //--- get command answer
        $commandReal = $this->connection->GetCommand($answer, $pos);
        if ($commandReal != $command)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $tickAnswer = new MTTickStatAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $tickAnswer->RetCode = $param['value'];
                    break;
                case MTProtocolConsts::WEB_PARAM_TRANS_ID:
                    $tickAnswer->TransId = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($tickAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //--- get json
        if (($tickAnswer->ConfigJson = $this->connection->GetJson($answer, $posEnd)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }
}
