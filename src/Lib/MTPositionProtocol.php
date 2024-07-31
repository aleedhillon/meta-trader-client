<?php
namespace Aleedhillon\MetaTraderClient\Lib;

//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2000-2021, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Class get positions
 */
class MTPositionProtocol
{
    private $m_connect; // connection to MT5 server
    /**
     * @param $connect MTConnect connect to MT5 server
     */
    public function __construct($connect)
    {
        $this->m_connect = $connect;
    }
    /**
     * Get position
     * @param int $login - login
     * @param string $symbol - symbol name
     * @param MTPosition $position
     * @return MTRetCode
     */
    public function PositionGet($login, $symbol, &$position)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login, MTProtocolConsts::WEB_PARAM_SYMBOL => $symbol);
        //---
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_POSITION_GET, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send position get failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer position get is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParsePosition(MTProtocolConsts::WEB_CMD_POSITION_GET, $answer, $position_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse position get failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //--- get object from json
        $position = $position_answer->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * check answer from MetaTrader 5 server
     * @param string $command command
     * @param  string $answer answer from server
     * @param  MTPositionAnswer $position_answer
     * @return MTRetCode
     */
    private function ParsePosition($command, &$answer, &$position_answer)
    {
        $pos = 0;
        //--- get command answer
        $command_real = $this->m_connect->GetCommand($answer, $pos);
        if ($command_real != $command)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $position_answer = new MTPositionAnswer();
        //--- get param
        $pos_end = -1;
        while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $position_answer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($ret_code = MTConnect::GetRetCode($position_answer->RetCode)) != MTRetCode::MT_RET_OK)
            return $ret_code;
        //--- get json
        if (($position_answer->ConfigJson = $this->m_connect->GetJson($answer, $pos_end)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * check answer from MetaTrader 5 server
     * @param  string $answer - answer from server
     * @param  MTPositionPageAnswer $position_answer
     * @return MTRetCode
     */
    private function ParsePositionPage(&$answer, &$position_answer)
    {
        $pos = 0;
        //--- get command answer
        $command_real = $this->m_connect->GetCommand($answer, $pos);
        if ($command_real != MTProtocolConsts::WEB_CMD_POSITION_GET_PAGE)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $position_answer = new MTPositionPageAnswer();
        //--- get param
        $pos_end = -1;
        while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $position_answer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($ret_code = MTConnect::GetRetCode($position_answer->RetCode)) != MTRetCode::MT_RET_OK)
            return $ret_code;
        //--- get json
        if (($position_answer->ConfigJson = $this->m_connect->GetJson($answer, $pos_end)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Get total positions for login
     * @param string $login - user login
     * @param int $total - count of users postions
     * @return MTRetCode
     */
    public function PositionGetTotal($login, &$total)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login);
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_POSITION_GET_TOTAL, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send position get total failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer position get total is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParsePositionTotal($answer, $position_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse position get total failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //--- get total
        $total = $position_answer->Total;
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Get positions
     * @param int $login - number of ticket
     * @param int $offset - begin records number
     * @param int $total - total records need
     * @param array(MTPosition) $positions
     * @return MTRetCode
     */
    public function PositionGetPage($login, $offset, $total, &$positions)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login, MTProtocolConsts::WEB_PARAM_OFFSET => $offset, MTProtocolConsts::WEB_PARAM_TOTAL => $total);
        //---
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_POSITION_GET_PAGE, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send position get page failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer position get page is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParsePositionPage($answer, $position_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse position get page failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //--- get object from json
        $positions = $position_answer->GetArrayFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Check answer from MetaTrader 5 server
     * @param  $answer string server answer
     * @param  $position_answer MTPositionTotalAnswer
     * @return false
     */
    private function ParsePositionTotal(&$answer, &$position_answer)
    {
        $pos = 0;
        //--- get command answer
        $command = $this->m_connect->GetCommand($answer, $pos);
        if ($command != MTProtocolConsts::WEB_CMD_POSITION_GET_TOTAL)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $position_answer = new MTPositionTotalAnswer();
        //--- get param
        $pos_end = -1;
        while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $position_answer->RetCode = $param['value'];
                    break;
                case MTProtocolConsts::WEB_PARAM_TOTAL:
                    $position_answer->Total = (int) $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($ret_code = MTConnect::GetRetCode($position_answer->RetCode)) != MTRetCode::MT_RET_OK)
            return $ret_code;
        //---
        return MTRetCode::MT_RET_OK;
    }
}
