<?php

namespace Aleedhillon\MetaTraderClient\Lib;

//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2000-2021, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Class authorization on MetaTrader 5 Server
 */
class MTAuthProtocol
{
    private $connection = null;
    private $agent = '';
    /**
     * @param MTConnect $connect connection to server
     * @param string $agent - name of agent
     * @return void
     */
    public function __construct($connect, $agent)
    {
        $this->connection = $connect;
        $this->agent = $agent;
    }
    /**
     * Authorization on MetaTrader 5 server
     * @param string $login - manager login
     * @param string $password - manager password
     * @param bool $isCrypt - need crypt connection
     * @param string $cryptRand - crypt rand string
     * @return int
     */
    public function Auth($login, $password, $isCrypt, &$cryptRand)
    {
        //--- send request to mt server
        if (($errorCode = $this->SendAuthStart($login, $isCrypt, $authStartAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //--- get code from hex string
        $randCode = MTUtils::GetFromHex($authStartAnswer->SrvRand);
        //--- random string for MT server
        $randomCliCode = MTUtils::GetRandomHex(16);
        //--- get hash password with random code
        $hash = MTUtils::GetHashFromPassword($password, $randCode);
        //--- send answer to server
        if (($errorCode = $this->SendAuthAnswer($hash, $randomCliCode, $authAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //--- check password with another random code from MT server
        $hashPassword = MTUtils::GetHashFromPassword($password, MTUtils::GetFromHex($randomCliCode));
        //--- check hash of password
        if ($hashPassword != $authAnswer->CliRand) {
            return MTRetCode::MT_RET_AUTH_SERVER_BAD;
        }
        //--- get crypt rand from MT server
        $cryptRand = $authAnswer->CryptRand;
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Send AUTH_ANSWER to MT server
     * @param string $hash - password hash
     * @param string $randomCliCode client random string
     * @param MTAuthAnswer $authAnswer - result from server
     * @return int
     */
    private function SendAuthAnswer($hash, $randomCliCode, &$authAnswer)
    {
        //--- send first request, with login, webapi version
        $data = array(
            MTProtocolConsts::WEB_PARAM_SRV_RAND_ANSWER => $hash,
            MTProtocolConsts::WEB_PARAM_CLI_RAND => $randomCliCode
        );
        //--- send request
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_AUTH_ANSWER, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read(true)) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseAuthAnswer($answer, $authAnswer, $error)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //--- ok
        return MTRetCode::MT_RET_OK;
    }
    /**
     * check answer from MetaTrader 5 server
     * @param  string $answer
     * @param  MTAuthStartAnswer $authAnswer
     * @param  string $error
     * @return int
     */
    private function ParseAuthStart(&$answer, &$authAnswer, &$error)
    {
        $pos = 0;
        //--- get command answer
        $command = $this->connection->GetCommand($answer, $pos);
        if ($command != MTProtocolConsts::WEB_CMD_AUTH_START) {
            $error = 'type answer "' . $command . '" is incorrect, is not ' . MTProtocolConsts::WEB_CMD_AUTH_START;
            return MTRetCode::MT_RET_ERR_DATA;
        }
        //---
        $authAnswer = new MTAuthStartAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $authAnswer->RetCode = $param['value'];
                    break;
                case MTProtocolConsts::WEB_PARAM_SRV_RAND:
                    $authAnswer->SrvRand = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($errorCode = MTConnect::GetRetCode($authAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $errorCode;
        //--- get srv rand
        if (empty($authAnswer->SrvRand) || $authAnswer->SrvRand == 'none') {
            $error = 'srv rand incorrect';
            return MTRetCode::MT_RET_ERR_PARAMS;
        }
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Send auth_start request
     * @param string $login  - user login
     * @param bool $isCrypt - need crypt protocol
     * @param MTAuthStartAnswer $authAnswer  - answer from server
     * @return int
     */
    private function SendAuthStart($login, $isCrypt, &$authAnswer)
    {
        //--- send first request, with login, webapi version
        $data = array(
            MTProtocolConsts::WEB_PARAM_VERSION => WebAPIVersion,
            MTProtocolConsts::WEB_PARAM_AGENT => $this->agent,
            MTProtocolConsts::WEB_PARAM_LOGIN => $login,
            MTProtocolConsts::WEB_PARAM_TYPE => 'MANAGER',
            MTProtocolConsts::WEB_PARAM_CRYPT_METHOD => $isCrypt
                ? MTProtocolConsts::WEB_VAL_CRYPT_AES256OFB : MTProtocolConsts::WEB_VAL_CRYPT_NONE
        );

        //--- send request
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_AUTH_START, $data, true))
            return MTRetCode::MT_RET_ERR_NETWORK;
        //--- get answer
        if (($answer = $this->connection->Read(true)) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseAuthStart($answer, $authAnswer, $error)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //---
        return MTRetCode::MT_RET_OK;
    }
    /**
     * Parse answer from request AUTH_ANSWER
     * @param string $answer - answer from server
     * @param MTAuthAnswer $authAnswer - result
     * @param string $error
     * @return int
     */
    private function ParseAuthAnswer($answer, &$authAnswer, &$error)
    {
        $pos = 0;
        //--- get command answer
        $command = $this->connection->GetCommand($answer, $pos);
        if ($command != MTProtocolConsts::WEB_CMD_AUTH_ANSWER) {
            $error = 'type answer "' . $command . '" is incorrect, is not ' . MTProtocolConsts::WEB_CMD_AUTH_ANSWER;
            return MTRetCode::MT_RET_ERR_DATA;
        }
        //---
        $authAnswer = new MTAuthAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                //--- ret code
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $authAnswer->RetCode = $param['value'];
                    break;
                //--- cli rand
                case MTProtocolConsts::WEB_PARAM_CLI_RAND_ANSWER:
                    $authAnswer->CliRand = $param['value'];
                    break;
                //--- crypt rand
                case MTProtocolConsts::WEB_PARAM_CRYPT_RAND:
                    $authAnswer->CryptRand = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($authAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //--- check CliRand
        if (empty($authAnswer->CliRand) || $authAnswer->CliRand == 'none') {
            $error = 'cli rand answer incorrect';
            return MTRetCode::MT_RET_ERR_PARAMS;
        }
        //---
        return MTRetCode::MT_RET_OK;
    }
}
