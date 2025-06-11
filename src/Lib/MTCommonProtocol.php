<?php

namespace Aleedhillon\MetaTraderClient\Lib;

//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2000-2021, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Class for send request common_get
 */
class MTCommonProtocol
{
    private MTConnect $connection;

    public function __construct(MTConnect $connect)
    {
        $this->connection = $connect;
    }

    /**
     * send request common_get
     * @param MTConCommon $common - config from MT5 server
     * @return int
     */
    public function CommonGet(&$common): int
    {
        //--- send request
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_COMMON_GET, "")) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        $commonAnswer = null;
        if (($errorCode = $this->ParseCommon($answer, $commonAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //--- get object from json
        $common = $commonAnswer->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * check answer from MetaTrader 5 server
     * @param  string $answer
     * @param  MTCommonGetAnswer $commonAnswer
     * @return int
     */
    private function ParseCommon(string &$answer, &$commonAnswer): int
    {
        $pos = 0;
        //--- get command answer
        $command = $this->connection->GetCommand($answer, $pos);
        if ($command != MTProtocolConsts::WEB_CMD_COMMON_GET)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $commonAnswer = new MTCommonGetAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $commonAnswer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($commonAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //--- get json
        if (($commonAnswer->ConfigJson = $this->connection->GetJson($answer, $pos)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }
}
