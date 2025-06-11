<?php

namespace Aleedhillon\MetaTraderClient\Lib;

//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2000-2021, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Class for send request get server time
 */
class MTTimeProtocol
{
    private $connection;

    public function __construct($connect)
    {
        $this->connection = $connect;
    }

    public function TimeServer()
    {
        //--- send request
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_TIME_SERVER, "")) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseTimeServer($answer, $timeAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }

        return $timeAnswer->Time;
    }

    private function ParseTimeServer(&$answer, &$timeAnswer)
    {
        $pos = 0;
        //--- get command answer
        $command = $this->connection->GetCommand($answer, $pos);
        if ($command != MTProtocolConsts::WEB_CMD_TIME_SERVER)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $timeAnswer = new MTTimeServerAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $timeAnswer->RetCode = $param['value'];
                    break;
                case MTProtocolConsts::WEB_PARAM_TIME:
                    $timeAnswer->Time = (int) $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($timeAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //---
        return MTRetCode::MT_RET_OK;
    }

    public function TimeGet(&$time)
    {
        //--- send request
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_TIME_GET, "")) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseTimeGet($answer, $timeGetAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //--- get object from json
        $time = $timeGetAnswer->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }

    private function ParseTimeGet(&$answer, &$timeAnswer)
    {
        $pos = 0;
        //--- get command answer
        $command = $this->connection->GetCommand($answer, $pos);
        if ($command != MTProtocolConsts::WEB_CMD_TIME_GET)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $timeAnswer = new MTTimeGetAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $timeAnswer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($timeAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //--- get json
        if (($timeAnswer->ConfigJson = $this->connection->GetJson($answer, $pos)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }
}
