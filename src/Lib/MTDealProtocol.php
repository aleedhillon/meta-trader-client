<?php
namespace Aleedhillon\MetaTraderClient\Lib;

//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2000-2021, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Class get deals
 */
class MTDealProtocol
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
     * Get dael
     * @param int $ticket - ticket
     * @param MTDeal $deal
     * @return MTRetCode
     */
    public function DealGet($ticket, &$deal)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_TICKET => $ticket);
        //---
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_DEAL_GET, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send deal get failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer deal get is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseDeal(MTProtocolConsts::WEB_CMD_DEAL_GET, $answer, $deal_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse deal get failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //--- get object from json
        $deal = $deal_answer->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * check answer from MetaTrader 5 server
     * @param string $command command
     * @param  string $answer answer from server
     * @param  MTDealAnswer $deal_answer
     * @return MTRetCode
     */
    private function ParseDeal($command, &$answer, &$deal_answer)
    {
        $pos = 0;
        //--- get command answer
        $command_real = $this->m_connect->GetCommand($answer, $pos);
        if ($command_real != $command)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $deal_answer = new MTDealAnswer();
        //--- get param
        $pos_end = -1;
        while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $deal_answer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($ret_code = MTConnect::GetRetCode($deal_answer->RetCode)) != MTRetCode::MT_RET_OK)
            return $ret_code;
        //--- get json
        if (($deal_answer->ConfigJson = $this->m_connect->GetJson($answer, $pos_end)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * check answer from MetaTrader 5 server
     * @param  string $answer - answer from server
     * @param  MTDealPageAnswer $deal_answer
     * @return MTRetCode
     */
    private function ParseDealPage(&$answer, &$deal_answer)
    {
        $pos = 0;
        //--- get command answer
        $command_real = $this->m_connect->GetCommand($answer, $pos);
        if ($command_real != MTProtocolConsts::WEB_CMD_DEAL_GET_PAGE)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $deal_answer = new MTDealPageAnswer();
        //--- get param
        $pos_end = -1;
        while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $deal_answer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($ret_code = MTConnect::GetRetCode($deal_answer->RetCode)) != MTRetCode::MT_RET_OK)
            return $ret_code;
        //--- get json
        if (($deal_answer->ConfigJson = $this->m_connect->GetJson($answer, $pos_end)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Get total deals for login
     * @param string $login - user login
     * @param int $from - date from
     * @param int $to - date to
     * @param int $total - count of users positions
     * @return MTRetCode
     */
    public function DealGetTotal($login, $from, $to, &$total)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login, MTProtocolConsts::WEB_PARAM_FROM => $from, MTProtocolConsts::WEB_PARAM_TO => $to);
        //---
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_DEAL_GET_TOTAL, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send deal get total failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer deal get total is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseDealTotal($answer, $deal_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse deal get total failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //--- get total
        $total = $deal_answer->Total;
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Get deals
     * @param int $login - number of ticket
     * @param int $from - from date in unix time
     * @param int $to - to date in unix time
     * @param int $offset - begin records number
     * @param int $total - total records need
     * @param array(MTDeal) $deals
     * @return MTRetCode
     */
    public function DealGetPage($login, $from, $to, $offset, $total, &$deals)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login, MTProtocolConsts::WEB_PARAM_FROM => $from, MTProtocolConsts::WEB_PARAM_TO => $to, MTProtocolConsts::WEB_PARAM_OFFSET => $offset, MTProtocolConsts::WEB_PARAM_TOTAL => $total);
        //---
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_DEAL_GET_PAGE, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send deal get page failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer deal get page is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseDealPage($answer, $deal_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse deal get page failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //--- get object from json
        $deals = $deal_answer->GetArrayFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Check answer from MetaTrader 5 server
     * @param  $answer string server answer
     * @param  $deal_answer MTDealTotalAnswer
     * @return false
     */
    private function ParseDealTotal(&$answer, &$deal_answer)
    {
        $pos = 0;
        //--- get command answer
        $command = $this->m_connect->GetCommand($answer, $pos);
        if ($command != MTProtocolConsts::WEB_CMD_DEAL_GET_TOTAL)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $deal_answer = new MTDealTotalAnswer();
        //--- get param
        $pos_end = -1;
        while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $deal_answer->RetCode = $param['value'];
                    break;
                case MTProtocolConsts::WEB_PARAM_TOTAL:
                    $deal_answer->Total = (int) $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($ret_code = MTConnect::GetRetCode($deal_answer->RetCode)) != MTRetCode::MT_RET_OK)
            return $ret_code;
        //---
        return MTRetCode::MT_RET_OK;
    }
}
