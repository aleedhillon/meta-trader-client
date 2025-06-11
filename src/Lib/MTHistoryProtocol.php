<?php

namespace Aleedhillon\MetaTraderClient\Lib;

//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2000-2021, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Class get history
 */
class MTHistoryProtocol
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
     * Get dael
     * @param int $ticket - ticket
     * @param MTOrder $history
     * @return int
     */
    public function HistoryGet($ticket, &$history)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_TICKET => $ticket);
        //---
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_HISTORY_GET, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseHistory(MTProtocolConsts::WEB_CMD_HISTORY_GET, $answer, $historyAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //--- get object from json
        $history = $historyAnswer->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * check answer from MetaTrader 5 server
     * @param string $command command
     * @param  string $answer answer from server
     * @param  MTHistoryAnswer $historyAnswer
     * @return int
     */
    private function ParseHistory($command, &$answer, &$historyAnswer)
    {
        $pos = 0;
        //--- get command answer
        $commandReal = $this->connection->GetCommand($answer, $pos);
        if ($commandReal != $command)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $historyAnswer = new MTHistoryAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $historyAnswer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($historyAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //--- get json
        if (($historyAnswer->ConfigJson = $this->connection->GetJson($answer, $posEnd)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * check answer from MetaTrader 5 server
     * @param  string $answer - answer from server
     * @param  MTHistoryPageAnswer $historyAnswer
     * @return int
     */
    private function ParseHistoryPage(&$answer, &$historyAnswer)
    {
        $pos = 0;
        //--- get command answer
        $commandReal = $this->connection->GetCommand($answer, $pos);
        if ($commandReal != MTProtocolConsts::WEB_CMD_HISTORY_GET_PAGE)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $historyAnswer = new MTHistoryPageAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $historyAnswer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($historyAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //--- get json
        if (($historyAnswer->ConfigJson = $this->connection->GetJson($answer, $posEnd)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Get total history for login
     * @param string $login - user login
     * @param int $from - date from
     * @param int $to - date to
     * @param int $total - count
     * @return int
     */
    public function HistoryGetTotal($login, $from, $to, &$total)
    {
        //--- send request
        $data = array(
            MTProtocolConsts::WEB_PARAM_LOGIN => $login,
            MTProtocolConsts::WEB_PARAM_FROM => $from,
            MTProtocolConsts::WEB_PARAM_TO => $to
        );
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_HISTORY_GET_TOTAL, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseHistoryTotal($answer, $historyAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //--- get total
        $total = $historyAnswer->Total;
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Get historys
     * @param int $login - number of ticket
     * @param int $from - from date in unix time
     * @param int $to - to date in unix time
     * @param int $offset - begin records number
     * @param int $total - total records need
     * @param array(MTOrder) $histories
     * @return int
     */
    public function HistoryGetPage($login, $from, $to, $offset, $total, &$histories)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login, MTProtocolConsts::WEB_PARAM_FROM => $from, MTProtocolConsts::WEB_PARAM_TO => $to, MTProtocolConsts::WEB_PARAM_OFFSET => $offset, MTProtocolConsts::WEB_PARAM_TOTAL => $total);
        //---
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_HISTORY_GET_PAGE, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseHistoryPage($answer, $historyAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //--- get object from json
        $histories = $historyAnswer->GetArrayFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Check answer from MetaTrader 5 server
     * @param  $answer string server answer
     * @param  $historyAnswer MTHistoryTotalAnswer
     * @return int
     */
    private function ParseHistoryTotal(&$answer, &$historyAnswer)
    {
        $pos = 0;
        //--- get command answer
        $command = $this->connection->GetCommand($answer, $pos);
        if ($command != MTProtocolConsts::WEB_CMD_HISTORY_GET_TOTAL)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $historyAnswer = new MTHistoryTotalAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {

                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $historyAnswer->RetCode = $param['value'];
                    break;
                case MTProtocolConsts::WEB_PARAM_TOTAL:
                    $historyAnswer->Total = (int) $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($historyAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //---
        return MTRetCode::MT_RET_OK;
    }
}
