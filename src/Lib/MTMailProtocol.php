<?php

namespace Aleedhillon\MetaTraderClient\Lib;

//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2000-2021, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Work with mail
 */
class MTMailProtocol
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
     * Send mail to user
     * @param string $to - user login or mask
     * @param string $subject - subject of mail
     * @param string $text - mail text, may be in html format
     * @return int
     */
    public function MailSend($to, $subject, $text)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_TO => $to, MTProtocolConsts::WEB_PARAM_SUBJECT => $subject, MTProtocolConsts::WEB_PARAM_BODYTEXT => $text);
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_MAIL_SEND, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        $tickAnswer = null;
        //---
        if (($errorCode = $this->Parse($answer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * check answer from MetaTrader 5 server
     * @param string $answer - answer from server
     * @return int
     */
    private function Parse(&$answer)
    {
        $pos = 0;
        //--- get command answer
        $commandReal = $this->connection->GetCommand($answer, $pos);
        if ($commandReal != MTProtocolConsts::WEB_CMD_MAIL_SEND)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $mailAnswer = new MTMailAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $mailAnswer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($mailAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //---
        return MTRetCode::MT_RET_OK;
    }
}
