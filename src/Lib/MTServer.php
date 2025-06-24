<?php

namespace Aleedhillon\MetaTraderClient\Lib;

//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2000-2021, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * class for control server
 */
class MTServer
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
     * Restart server
     *
     * @return int
     */
    public function Restart(): int
    {
        //--- send request

        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_SERVER_RESTART, '')) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        $restartAnswer = null;
        //---
        if (($errorCode = $this->Parse($answer, $restartAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * check answer from MetaTrader 5 server
     *
     * @param string           $answer - answer from server
     * @param  MTRestartAnswer $restartAnswer
     *
     * @return int
     */
    private function Parse(&$answer, &$restartAnswer): int
    {
        $pos = 0;
        //--- get command answer
        $commandReal = $this->connection->GetCommand($answer, $pos);
        if ($commandReal != MTProtocolConsts::WEB_CMD_SERVER_RESTART)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $restartAnswer = new MTRestartAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $restartAnswer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($restartAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //---
        return MTRetCode::MT_RET_OK;
    }
}
