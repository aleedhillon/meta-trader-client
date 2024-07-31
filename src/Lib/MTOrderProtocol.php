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
    private $m_connect; // connection to MT5 server
    /**
     * @param MTConnect $connect - connect to MT5 server
     */
    public function __construct($connect)
    {
        $this->m_connect = $connect;
    }
    /**
     * Get order
     * @param string $ticket - number of ticket
     * @param MTOrder $order
     * @return MTRetCode
     */
    public function OrderGet($ticket, &$order)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_TICKET => $ticket);
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_ORDER_GET, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send order get failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer order get is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseOrder(MTProtocolConsts::WEB_CMD_ORDER_GET, $answer, $order_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse order get failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //--- get object from json
        $order = $order_answer->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * check answer from MetaTrader 5 server
     * @param string $command - command
     * @param string $answer - answer from server
     * @param  MTOrderAnswer $order_answer
     * @return MTRetCode
     */
    private function ParseOrder($command, &$answer, &$order_answer)
    {
        $pos = 0;
        //--- get command answer
        $command_real = $this->m_connect->GetCommand($answer, $pos);
        if ($command_real != $command)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $order_answer = new MTOrderAnswer();
        //--- get param
        $pos_end = -1;
        while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $order_answer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($ret_code = MTConnect::GetRetCode($order_answer->RetCode)) != MTRetCode::MT_RET_OK)
            return $ret_code;
        //--- get json
        if (($order_answer->ConfigJson = $this->m_connect->GetJson($answer, $pos_end)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * check answer from MetaTrader 5 server
     * @param  string $answer - answer from server
     * @param  MTOrderPageAnswer $order_answer
     * @return MTRetCode
     */
    private function ParseOrderPage(&$answer, &$order_answer)
    {
        $pos = 0;
        //--- get command answer
        $command_real = $this->m_connect->GetCommand($answer, $pos);
        if ($command_real != MTProtocolConsts::WEB_CMD_ORDER_GET_PAGE)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $order_answer = new MTOrderPageAnswer();
        //--- get param
        $pos_end = -1;
        while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $order_answer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($ret_code = MTConnect::GetRetCode($order_answer->RetCode)) != MTRetCode::MT_RET_OK)
            return $ret_code;
        //--- get json
        if (($order_answer->ConfigJson = $this->m_connect->GetJson($answer, $pos_end)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Get total order for login
     * @param string $login - user login
     * @param int $total - count of users orders
     * @return MTRetCode
     */
    public function OrderGetTotal($login, &$total)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login);
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_ORDER_GET_TOTAL, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send order get total failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer order get total is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseOrderTotal($answer, $order_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse order get total failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //--- get total
        $total = $order_answer->Total;
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Get order
     * @param int $login - number of ticket
     * @param int $offset - begin records number
     * @param int $total - total records need
     * @param array(MTOrder) $orders
     * @return MTRetCode
     */
    public function OrderGetPage($login, $offset, $total, &$orders)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login, MTProtocolConsts::WEB_PARAM_OFFSET => $offset, MTProtocolConsts::WEB_PARAM_TOTAL => $total);
        //---
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_ORDER_GET_PAGE, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send order get page failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer order get page is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseOrderPage($answer, $order_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse order get page failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //--- get object from json
        $orders = $order_answer->GetArrayFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Check answer from MetaTrader 5 server
     * @param  $answer string server answer
     * @param  $order_answer MTOrderTotalAnswer
     * @return false
     */
    private function ParseOrderTotal(&$answer, &$order_answer)
    {
        $pos = 0;
        //--- get command answer
        $command = $this->m_connect->GetCommand($answer, $pos);
        if ($command != MTProtocolConsts::WEB_CMD_ORDER_GET_TOTAL)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $order_answer = new MTOrderTotalAnswer();
        //--- get param
        $pos_end = -1;
        while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $order_answer->RetCode = $param['value'];
                    break;
                case MTProtocolConsts::WEB_PARAM_TOTAL:
                    $order_answer->Total = (int) $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($ret_code = MTConnect::GetRetCode($order_answer->RetCode)) != MTRetCode::MT_RET_OK)
            return $ret_code;
        //---
        return MTRetCode::MT_RET_OK;
    }
}
