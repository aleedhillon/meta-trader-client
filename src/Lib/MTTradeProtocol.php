<?php

namespace Aleedhillon\MetaTraderClient\Lib;

//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2000-2021, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
class MTTradeProtocol
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
     * Set balance
     *
     * @param int            $login user login
     * @param MTEnDealAction $type
     * @param double         $balance
     * @param string         $comment
     * @param int            $ticket
     * @param bool           $marginCheck
     *
     * @return int
     */
    public function TradeBalance($login, $type, $balance, $comment, &$ticket = null, $marginCheck = true)
    {
        //--- send request
        $data = array(
            MTProtocolConsts::WEB_PARAM_LOGIN => $login,
            MTProtocolConsts::WEB_PARAM_TYPE => $type,
            MTProtocolConsts::WEB_PARAM_BALANCE => $balance,
            MTProtocolConsts::WEB_PARAM_COMMENT => $comment,
            MTProtocolConsts::WEB_PARAM_CHECK_MARGIN => $marginCheck ? "1" : "0",
        );
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_TRADE_BALANCE, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        $tradeAnswer = null;
        //---
        if (($errorCode = $this->Parse($answer, $tradeAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //---
        $ticket = $tradeAnswer->Ticket;
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * check answer from MetaTrader 5 server
     *
     * @param string         $answer - answer from server
     * @param  MTTradeAnswer $tradeAnswer
     *
     * @return int
     */
    private function Parse(&$answer, &$tradeAnswer)
    {
        $pos = 0;
        //--- get command answer
        $commandReal = $this->connection->GetCommand($answer, $pos);
        if ($commandReal != MTProtocolConsts::WEB_CMD_TRADE_BALANCE)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $tradeAnswer = new MTTradeAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $tradeAnswer->RetCode = $param['value'];
                    break;
                case MTProtocolConsts::WEB_PARAM_TICKET:
                    $tradeAnswer->Ticket = (int) $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($tradeAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //---
        return MTRetCode::MT_RET_OK;
    }
}
