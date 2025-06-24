<?php

namespace Aleedhillon\MetaTraderClient\Lib;

//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2000-2021, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Class get positions
 */
class MTPositionProtocol
{
    private $connection; // connection to MT5 server
    /**
     * @param $connect MTConnect connect to MT5 server
     */
    public function __construct($connect)
    {
        $this->connection = $connect;
    }
    /**
     * Get position
     * @param int $login - login
     * @param string $symbol - symbol name
     * @param MTPosition $position
     * @return int
     */
    public function PositionGet($login, $symbol, &$position)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login, MTProtocolConsts::WEB_PARAM_SYMBOL => $symbol);
        //---
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_POSITION_GET, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParsePosition(MTProtocolConsts::WEB_CMD_POSITION_GET, $answer, $positionAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //--- get object from json
        $position = $positionAnswer->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * check answer from MetaTrader 5 server
     * @param string $command command
     * @param  string $answer answer from server
     * @param  MTPositionAnswer $positionAnswer
     * @return int
     */
    private function ParsePosition($command, &$answer, &$positionAnswer)
    {
        $pos = 0;
        //--- get command answer
        $commandReal = $this->connection->GetCommand($answer, $pos);
        if ($commandReal != $command)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $positionAnswer = new MTPositionAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $positionAnswer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($positionAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //--- get json
        if (($positionAnswer->ConfigJson = $this->connection->GetJson($answer, $posEnd)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * check answer from MetaTrader 5 server
     * @param  string $answer - answer from server
     * @param  MTPositionPageAnswer $positionAnswer
     * @return int
     */
    private function ParsePositionPage(&$answer, &$positionAnswer)
    {
        $pos = 0;
        //--- get command answer
        $commandReal = $this->connection->GetCommand($answer, $pos);
        if ($commandReal != MTProtocolConsts::WEB_CMD_POSITION_GET_PAGE)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $positionAnswer = new MTPositionPageAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $positionAnswer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($positionAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //--- get json
        if (($positionAnswer->ConfigJson = $this->connection->GetJson($answer, $posEnd)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Get total positions for login
     * @param string $login - user login
     * @param int $total - count of users postions
     * @return int
     */
    public function PositionGetTotal($login, &$total)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login);
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_POSITION_GET_TOTAL, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParsePositionTotal($answer, $positionAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //--- get total
        $total = $positionAnswer->Total;
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Get positions
     * @param int $login - number of ticket
     * @param int $offset - begin records number
     * @param int $total - total records need
     * @param array(MTPosition) $positions
     * @return int
     */
    public function PositionGetPage($login, $offset, $total, &$positions)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login, MTProtocolConsts::WEB_PARAM_OFFSET => $offset, MTProtocolConsts::WEB_PARAM_TOTAL => $total);
        //---
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_POSITION_GET_PAGE, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParsePositionPage($answer, $positionAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //--- get object from json
        $positions = $positionAnswer->GetArrayFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Check answer from MetaTrader 5 server
     * @param  $answer string server answer
     * @param  $positionAnswer MTPositionTotalAnswer
     * @return int
     */
    private function ParsePositionTotal(&$answer, &$positionAnswer)
    {
        $pos = 0;
        //--- get command answer
        $command = $this->connection->GetCommand($answer, $pos);
        if ($command != MTProtocolConsts::WEB_CMD_POSITION_GET_TOTAL)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $positionAnswer = new MTPositionTotalAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $positionAnswer->RetCode = $param['value'];
                    break;
                case MTProtocolConsts::WEB_PARAM_TOTAL:
                    $positionAnswer->Total = (int) $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($positionAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //---
        return MTRetCode::MT_RET_OK;
    }
}
