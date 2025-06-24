<?php

namespace Aleedhillon\MetaTraderClient\Lib;

//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2000-2021, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Work with news
 */
class MTNewsProtocol
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
     * Send news to users
     * @param string $subject - subject of news
     * @param string $category
     * @param int $language
     * @param int $priority
     * @param string $text - news text, may be in html format
     * @return int
     */
    public function NewsSend($subject, $category, $language, $priority, $text)
    {
        //--- send request
        $data = array(
            MTProtocolConsts::WEB_PARAM_SUBJECT => $subject,
            MTProtocolConsts::WEB_PARAM_CATEGORY => $category,
            MTProtocolConsts::WEB_PARAM_LANGUAGE => $language,
            MTProtocolConsts::WEB_PARAM_PRIORITY => $priority,
            MTProtocolConsts::WEB_PARAM_BODYTEXT => $text
        );
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_NEWS_SEND, $data)) {
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
        if ($commandReal != MTProtocolConsts::WEB_CMD_NEWS_SEND)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $newsAnswer = new MTNewsAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $newsAnswer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($newsAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //---
        return MTRetCode::MT_RET_OK;
    }
}
