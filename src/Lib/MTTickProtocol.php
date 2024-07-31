<?php
namespace Aleedhillon\MetaTraderClient\Lib;

//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2000-2021, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Work with tick
 */
class MTTickProtocol
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
     * Get last ticks
     * @param string $symbol - name symbol
     * @param array(MTTick) $ticks
     * @return MTRetCode
     */
    public function TickLast($symbol, &$ticks)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_SYMBOL => $symbol);
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_TICK_LAST, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send tick last failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer tick last is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        $tick_answer = null;

        if (($error_code = $this->Parse(MTProtocolConsts::WEB_CMD_TICK_LAST, $answer, $tick_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse tick last failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //--- get object from json
        $ticks = $tick_answer->GetArrayFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * check answer from MetaTrader 5 server
     * @param string $command - command
     * @param string $answer - answer from server
     * @param  MTTickAnswer $tick_answer
     * @return MTRetCode
     */
    private function Parse($command, &$answer, &$tick_answer)
    {
        $pos = 0;
        //--- get command answer
        $command_real = $this->m_connect->GetCommand($answer, $pos);
        if ($command_real != $command)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $tick_answer = new MTTickAnswer();
        //--- get param
        $pos_end = -1;
        while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $tick_answer->RetCode = $param['value'];
                    break;
                case MTProtocolConsts::WEB_PARAM_TRANS_ID:
                    $tick_answer->TransId = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($ret_code = MTConnect::GetRetCode($tick_answer->RetCode)) != MTRetCode::MT_RET_OK)
            return $ret_code;
        //--- get json
        if (($tick_answer->ConfigJson = $this->m_connect->GetJson($answer, $pos_end)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Get last tickets by symbol and group
     * @param string $symbol
     * @param string $group
     * @param array(MTTick) $ticks
     * @return MTRetCode
     */
    public function TickLastGroup($symbol, $group, &$ticks)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_SYMBOL => $symbol, MTProtocolConsts::WEB_PARAM_GROUP => $group);
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_TICK_LAST_GROUP, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send tick last group failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer tick last group is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        $tick_answer = null;
        //---
        if (($error_code = $this->Parse(MTProtocolConsts::WEB_CMD_TICK_LAST_GROUP, $answer, $tick_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse tick last group failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //--- get object from json
        $ticks = $tick_answer->GetArrayFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Get stat ticks
     * @param string $symbol - name symbol
     * @param array(MTTickStat) $tick_stat
     * @return MTRetCode
     */
    public function TickStat($symbol, &$tick_stat)
    {
        //--- send request
        $data = array(MTProtocolConsts::WEB_PARAM_SYMBOL => $symbol);
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_TICK_STAT, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send tick last failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer tick last is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        $tick_answer = null;

        if (($error_code = $this->ParseTickStat(MTProtocolConsts::WEB_CMD_TICK_STAT, $answer, $tick_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse tick last failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //--- get object from json
        $tick_stat = $tick_answer->GetArrayFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * check answer from MetaTrader 5 server
     * @param string $command - command
     * @param string $answer - answer from server
     * @param  MTTickAnswer $tick_answer
     * @return MTRetCode
     */
    private function ParseTickStat($command, &$answer, &$tick_answer)
    {
        $pos = 0;
        //--- get command answer
        $command_real = $this->m_connect->GetCommand($answer, $pos);
        if ($command_real != $command)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $tick_answer = new MTTickStatAnswer();
        //--- get param
        $pos_end = -1;
        while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $tick_answer->RetCode = $param['value'];
                    break;
                case MTProtocolConsts::WEB_PARAM_TRANS_ID:
                    $tick_answer->TransId = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($ret_code = MTConnect::GetRetCode($tick_answer->RetCode)) != MTRetCode::MT_RET_OK)
            return $ret_code;
        //--- get json
        if (($tick_answer->ConfigJson = $this->m_connect->GetJson($answer, $pos_end)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }
}
