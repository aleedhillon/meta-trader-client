<?php

namespace Aleedhillon\MetaTraderClient\Lib;

//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2000-2021, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Class get order
 */
class MTOrderProtocol
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
     * Get order
     * @param string $ticket - number of ticket
     * @param MTOrder $order
     * @return int
     */
    public function OrderGet($ticket, &$order)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_TICKET => $ticket);
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_ORDER_GET, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseOrder(MTProtocolConsts::WEB_CMD_ORDER_GET, $answer, $orderAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //--- get object from json
        $order = $orderAnswer->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * check answer from MetaTrader 5 server
     * @param string $command - command
     * @param string $answer - answer from server
     * @param  MTOrderAnswer $orderAnswer
     * @return int
     */
    private function ParseOrder($command, &$answer, &$orderAnswer)
    {
        $pos = 0;
        //--- get command answer
        $commandReal = $this->connection->GetCommand($answer, $pos);
        if ($commandReal != $command)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $orderAnswer = new MTOrderAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $orderAnswer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($orderAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //--- get json
        if (($orderAnswer->ConfigJson = $this->connection->GetJson($answer, $posEnd)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * check answer from MetaTrader 5 server
     * @param  string $answer - answer from server
     * @param  MTOrderPageAnswer $orderAnswer
     * @return int
     */
    private function ParseOrderPage(&$answer, &$orderAnswer)
    {
        $pos = 0;
        //--- get command answer
        $commandReal = $this->connection->GetCommand($answer, $pos);
        if ($commandReal != MTProtocolConsts::WEB_CMD_ORDER_GET_PAGE)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $orderAnswer = new MTOrderPageAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $orderAnswer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($orderAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //--- get json
        if (($orderAnswer->ConfigJson = $this->connection->GetJson($answer, $posEnd)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Get total order for login
     * @param string $login - user login
     * @param int $total - count of users orders
     * @return int
     */
    public function OrderGetTotal($login, &$total)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login);
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_ORDER_GET_TOTAL, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseOrderTotal($answer, $orderAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //--- get total
        $total = $orderAnswer->Total;
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Get order
     * @param int $login - number of ticket
     * @param int $offset - begin records number
     * @param int $total - total records need
     * @param array(MTOrder) $orders
     * @return int
     */
    public function OrderGetPage($login, $offset, $total, &$orders)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login, MTProtocolConsts::WEB_PARAM_OFFSET => $offset, MTProtocolConsts::WEB_PARAM_TOTAL => $total);
        //---
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_ORDER_GET_PAGE, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseOrderPage($answer, $orderAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //--- get object from json
        $orders = $orderAnswer->GetArrayFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Check answer from MetaTrader 5 server
     * @param  $answer string server answer
     * @param  $orderAnswer MTOrderTotalAnswer
     * @return int
     */
    private function ParseOrderTotal(&$answer, &$orderAnswer)
    {
        $pos = 0;
        //--- get command answer
        $command = $this->connection->GetCommand($answer, $pos);
        if ($command != MTProtocolConsts::WEB_CMD_ORDER_GET_TOTAL)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $orderAnswer = new MTOrderTotalAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $orderAnswer->RetCode = $param['value'];
                    break;
                case MTProtocolConsts::WEB_PARAM_TOTAL:
                    $orderAnswer->Total = (int) $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($orderAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //---
        return MTRetCode::MT_RET_OK;
    }
}
