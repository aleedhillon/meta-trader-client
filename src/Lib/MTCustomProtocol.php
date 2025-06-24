<?php

namespace Aleedhillon\MetaTraderClient\Lib;

//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2000-2021, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
class MTCustomProtocol
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
     * Send custom command to MT server
     * @param string $command
     * @param array $params
     * @param string $body
     * @param array $answerCustom
     * @param string $answerBody
     * @return int
     */
    public function CustomSend($command, $params, $body, &$answerCustom, &$answerBody)
    {
        //--- send request
        $data = $params;
        //---
        if (!empty($body)) {
            if (empty($data))
                $data = array();
            $data[MTProtocolConsts::WEB_PARAM_BODYTEXT] = $body;
        }
        //---
        if (!$this->connection->Send($command, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read(false, true)) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        $tradeAnswer = null;

        if (($errorCode = $this->Parse($command, $answer, $answerCustom, $answerBody)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * check answer from MetaTrader 5 server
     * @param string $command - command from server
     * @param string $answer - answer from server
     * @param  array $customAnswer
     * @param  string $answerBody
     * @return int
     */
    private function Parse($command, &$answer, &$customAnswer, &$answerBody)
    {
        $pos = 0;
        //--- get command answer
        $commandReal = $this->connection->GetCommand($answer, $pos);
        if ($commandReal != $command)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $customAnswer = array();
        //--- get param
        $posEnd = -1;
        $retCode = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $retCode = $param['value'];
                    break;
            }
            $customAnswer[$param['name']] = $param['value'];
        }
        //--- get body
        $answerBody = $this->connection->GetBinary($answer);
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($retCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //---
        return MTRetCode::MT_RET_OK;
    }
}
