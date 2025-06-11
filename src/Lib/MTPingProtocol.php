<?php

namespace Aleedhillon\MetaTraderClient\Lib;

//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2000-2021, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * send ping to MT server
 */
class MTPingProtocol
{
    private $connection;

    public function __construct($connect)
    {
        $this->connection = $connect;
    }

    /**
     * Send ping to MetaTrader 5 server
     * @return MTRetCode|int
     */
    public function PingSend()
    {
        //--- send request
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_PING, "")) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        return $this->ParsePing($answer);
    }

    /**
     * check answer from MetaTrader 5 server
     * @param string $answer
     * @return MTRetCode|int
     */
    private function ParsePing(&$answer)
    {
        $pos = 0;
        //--- get command answer
        $command = $this->connection->GetCommand($answer, $pos);
        if ($command != MTProtocolConsts::WEB_CMD_PING)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $retCode = null;
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $retCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($result = MTConnect::GetRetCode($retCode)) != MTRetCode::MT_RET_OK)
            return $result;
        //---
        return MTRetCode::MT_RET_OK;
    }
}
