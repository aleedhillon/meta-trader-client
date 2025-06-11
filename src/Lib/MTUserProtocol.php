<?php

namespace Aleedhillon\MetaTraderClient\Lib;

//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2000-2021, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Class work with users
 */
class MTUserProtocol
{
    private $connection; // connection to MT5 server
    /**
     * @param $connect MTConnect connect to MT5 server
     */
    public function __construct($connect)
    {
        $this->connection = $connect;
    }

    /**
     * Add new user
     *
     * @param $user     MTUser information about user
     * @param $newUser MTUser information about user getting from server
     *
     * @return int
     */
    public function Add($user, &$newUser)
    {
        //--- send request
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_USER_ADD, $this->GetParamAdd($user))) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseAddUser($answer, $userAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //---
        $newUser = $userAnswer->GetFromJson();
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Check answer from MetaTrader 5 server
     *
     * @param  $answer      string answer from server
     * @param  $userAnswer MTUserAnswer
     *
     * @return int
     */
    private function ParseAddUser(&$answer, &$userAnswer)
    {
        $pos = 0;
        //--- get command answer
        $commandReal = $this->connection->GetCommand($answer, $pos);
        if ($commandReal != MTProtocolConsts::WEB_CMD_USER_ADD)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $userAnswer = new MTUserAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $userAnswer->RetCode = $param['value'];
                    break;
                //---
                case MTProtocolConsts::WEB_PARAM_LOGIN:
                    $userAnswer->Login = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($userAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //--- check login
        if (empty($userAnswer->Login))
            return MTRetCode::MT_RET_ERR_PARAMS;
        //--- get json
        if (($userAnswer->ConfigJson = $this->connection->GetJson($answer, $posEnd)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Check answer from MetaTrader 5 server
     *
     * @param  $command     string command
     * @param  $answer      string answer from server
     * @param  $userAnswer MTUserAnswer
     *
     * @return int
     */
    private function ParseUser($command, &$answer, &$userAnswer)
    {
        $pos = 0;
        //--- get command answer
        $commandReal = $this->connection->GetCommand($answer, $pos);
        if ($commandReal != $command)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $userAnswer = new MTUserAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $userAnswer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($userAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //--- get json
        if (($userAnswer->ConfigJson = $this->connection->GetJson($answer, $posEnd)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Update user
     *
     * @param $user     MTUser information about user
     * @param $newUser MTUser information about user getting from server
     *
     * @return int
     */
    public function Update($user, &$newUser)
    {
        //--- send request
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_USER_UPDATE, $this->GetParamUpdate($user))) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseUser(MTProtocolConsts::WEB_CMD_USER_UPDATE, $answer, $userAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //---
        $newUser = $userAnswer->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Update user
     *
     * @param $login int login
     * @param $user  MTUser information about user getting from server
     *
     * @return int
     */
    public function Get($login, &$user)
    {
        $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login);
        //--- send request
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_USER_GET, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseUser(MTProtocolConsts::WEB_CMD_USER_GET, $answer, $userAnswer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //---
        $user = $userAnswer->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Update user
     *
     * @param $login int login
     *
     * @return int
     */
    public function Delete($login)
    {
        $login = (int) $login;
        $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login);
        //--- send request
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_USER_DELETE, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseClearCommand(MTProtocolConsts::WEB_CMD_USER_DELETE, $answer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Check answer from MetaTrader 5 server
     *
     * @param  $command string command
     * @param  $answer  string answer from server
     *
     * @return int
     */
    private function ParseClearCommand($command, &$answer)
    {
        $pos = 0;
        //--- get command answer
        $commandReal = $this->connection->GetCommand($answer, $pos);
        if ($commandReal != $command)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $userAnswer = new MTUserAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $userAnswer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($userAnswer->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * check user password
     *
     * @param        $login    int
     * @param        $password string
     * @param string $type     WEB_VAL_USER_PASS_MAIN | WEB_VAL_USER_PASS_INVESTOR
     *
     * @return int
     */
    public function PasswordCheck($login, $password, $type = MTProtocolConsts::WEB_VAL_USER_PASS_MAIN)
    {
        $login = (int) $login;
        //--- send request
        $data = array(
            MTProtocolConsts::WEB_PARAM_LOGIN => $login,
            MTProtocolConsts::WEB_PARAM_TYPE => $type,
            MTProtocolConsts::WEB_PARAM_PASSWORD => $password
        );
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_USER_PASS_CHECK, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseClearCommand(MTProtocolConsts::WEB_CMD_USER_PASS_CHECK, $answer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * user password change
     *
     * @param        $login        int
     * @param        $newPassword string new password
     * @param string $type         WEB_VAL_USER_PASS_MAIN | WEB_VAL_USER_PASS_INVESTOR
     *
     * @return int
     */
    public function PasswordChange($login, $newPassword, $type = MTProtocolConsts::WEB_VAL_USER_PASS_MAIN)
    {
        $login = (int) $login;
        //--- send request
        $data = array(
            MTProtocolConsts::WEB_PARAM_LOGIN => $login,
            MTProtocolConsts::WEB_PARAM_TYPE => $type,
            MTProtocolConsts::WEB_PARAM_PASSWORD => $newPassword
        );
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_USER_PASS_CHANGE, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseClearCommand(MTProtocolConsts::WEB_CMD_USER_PASS_CHANGE, $answer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * user deposit change
     *
     * @param $login       int
     * @param $newDeposit float deposit
     * @param $comment     string comment
     * @param $type        MTEnDealAction type of balance: DEAL_BALANCE, DEAL_CREDIT, DEAL_CHARGE, DEAL_BONUS
     *
     * @return int
     */
    public function DepositChange($login, $newDeposit, $comment, $type)
    {
        $login = (int) $login;
        //--- send request
        $data = array(
            MTProtocolConsts::WEB_PARAM_LOGIN => $login,
            MTProtocolConsts::WEB_PARAM_TYPE => $type,
            MTProtocolConsts::WEB_PARAM_BALANCE => $newDeposit,
            MTProtocolConsts::WEB_PARAM_COMMENT => $comment
        );
        //--
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_USER_DEPOSIT_CHANGE, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseClearCommand(MTProtocolConsts::WEB_CMD_USER_DEPOSIT_CHANGE, $answer)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * user acount get
     *
     * @param $login   int
     * @param $account MTAccount
     *
     * @return int
     */
    public function AccountGet($login, &$account)
    {
        $login = (int) $login;
        $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login);
        //--- send request
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_USER_ACCOUNT_GET, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($errorCode = $this->ParseUserAccount(MTProtocolConsts::WEB_CMD_USER_ACCOUNT_GET, $answer, $userAccount)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //---
        $account = $userAccount->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * parsing answer for command user account get
     *
     * @param $command      MTProtocolConsts
     * @param $answer       string
     * @param $userAccount MTUserAccountAnswer
     *
     * @return int
     */
    private function ParseUserAccount($command, $answer, &$userAccount)
    {
        $pos = 0;
        //--- get command answer
        $commandReal = $this->connection->GetCommand($answer, $pos);
        if ($commandReal != $command)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $userAccount = new MTUserAccountAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $userAccount->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($userAccount->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //--- get json
        if (($userAccount->ConfigJson = $this->connection->GetJson($answer, $posEnd)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Get list users login
     *
     * @param string     $group
     * @param array(int) $logins
     *
     * @return int
     */
    public function UserLogins($group, &$logins)
    {
        $data = array(MTProtocolConsts::WEB_PARAM_GROUP => $group);
        //--- send request
        if (!$this->connection->Send(MTProtocolConsts::WEB_CMD_USER_USER_LOGINS, $data)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->connection->Read()) == null) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        $userLogins = null;
        //--- parse answer
        if (($errorCode = $this->ParseUserLogins($answer, $userLogins)) != MTRetCode::MT_RET_OK) {
            return $errorCode;
        }
        //---
        $logins = $userLogins ? $userLogins->GetFromJson() : [];
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * parsing answer for command user_logins
     *
     * @param $answer       string
     * @param $userAccount MTUserAccountAnswer
     *
     * @return int
     */
    private function ParseUserLogins($answer, &$userAccount)
    {
        $pos = 0;
        //--- get command answer
        $commandReal = $this->connection->GetCommand($answer, $pos);
        if ($commandReal != MTProtocolConsts::WEB_CMD_USER_USER_LOGINS)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $userAccount = new MTUserLoginsAnswer();
        //--- get param
        $posEnd = -1;
        while (($param = $this->connection->GetNextParam($answer, $pos, $posEnd)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $userAccount->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($retCode = MTConnect::GetRetCode($userAccount->RetCode)) != MTRetCode::MT_RET_OK)
            return $retCode;
        //--- get json
        if (($userAccount->ConfigJson = $this->connection->GetJson($answer, $posEnd)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * check all fields on null
     *
     * @param MTUser $user
     */
    private function CheckNull(&$user)
    {
        //--- login
        if ($user->Login == null)
            $user->Login = 0;
        //--- group
        if ($user->Group == null)
            $user->Group = "";
        //--- certificate serial number
        if ($user->CertSerialNumber == null)
            $user->CertSerialNumber = 0;
        //--- MTEnUsersRights
        if ($user->Rights == null)
            $user->Rights = 0;
        //--- MQID
        if ($user->MQID == null)
            $user->MQID = "";
        //--- registration datetime (filled by MT5)
        if ($user->Registration == null)
            $user->Registration = 0;
        if ($user->LastAccess == null)
            $user->LastAccess = 0;
        if ($user->LastPassChange == null)
            $user->LastPassChange = 0;
        if ($user->LastIP == null)
            $user->LastIP = "";
        //--- name
        if ($user->Name == null)
            $user->Name = "";
        //--- company
        if ($user->Company == null)
            $user->Company = "";
        //--- external system account (exchange, ECN, etc)
        if ($user->Account == null)
            $user->Account = "";
        //--- country
        if ($user->Country == null)
            $user->Country = "";
        //--- client language (WinAPI LANGID)
        if ($user->Language == null)
            $user->Language = 0;
        //--- client id
        if ($user->ClientID == null)
            $user->ClientID = 0;
        //--- city
        if ($user->City == null)
            $user->City = "";
        //--- state
        if ($user->State == null)
            $user->State = "";
        //--- ZIP code
        if ($user->ZipCode == null)
            $user->ZipCode = "";
        //--- address
        if ($user->Address == null)
            $user->Address = "";
        //--- phone
        if ($user->Phone == null)
            $user->Phone = "";
        //--- email
        if ($user->Email == null)
            $user->Email = "";
        //--- additional ID
        if ($user->ID == null)
            $user->ID = "";
        //--- additional status
        if ($user->Status == null)
            $user->Status = "";
        //--- comment
        if ($user->Comment == null)
            $user->Comment = "";
        //--- color
        if ($user->Color == null)
            $user->Color = 0;
        //--- phone password
        if ($user->PhonePassword == null)
            $user->PhonePassword = "";
        //--- leverage
        if ($user->Leverage == null)
            $user->Leverage = 0;
        //--- agent account
        if ($user->Agent == null)
            $user->Agent = 0;
        //--- main password
        if ($user->MainPassword == null)
            $user->MainPassword = "";
        //--- invest password
        if ($user->InvestPassword == null)
            $user->InvestPassword = "";
        //--- balance & credit
        if ($user->Balance == null)
            $user->Balance = 0;
        if ($user->Credit == null)
            $user->Credit = 0;
        //--- accumulated interest rate
        if ($user->InterestRate == null)
            $user->InterestRate = 0;
        //--- accumulated daily and monthly commissions
        if ($user->CommissionDaily == null)
            $user->CommissionDaily = 0;
        if ($user->CommissionMonthly == null)
            $user->CommissionMonthly = 0;
        //--- previous balance state
        if ($user->BalancePrevDay == null)
            $user->BalancePrevDay = 0;
        if ($user->BalancePrevMonth == null)
            $user->BalancePrevMonth = 0;
        //--- previous equity state
        if ($user->EquityPrevDay == null)
            $user->EquityPrevDay = 0;
        if ($user->EquityPrevMonth == null)
            $user->EquityPrevMonth = 0;
        //--- external trade accounts
        if ($user->TradeAccounts == null)
            $user->TradeAccounts = "";
        //--- leads
        if ($user->LeadCampaign == null)
            $user->LeadCampaign = "";
        if ($user->LeadSource == null)
            $user->LeadSource = "";
    }

    /**
     * Get array of params for sending to MetaTrader 5 server
     *
     * @param $user MTUser
     *
     * @return array
     */
    private function GetParamAdd($user)
    {
        $this->CheckNull($user);
        return array(
            MTProtocolConsts::WEB_PARAM_LOGIN => $user->Login,
            MTProtocolConsts::WEB_PARAM_PASS_MAIN => $user->MainPassword,
            MTProtocolConsts::WEB_PARAM_PASS_INVESTOR => $user->InvestPassword,
            MTProtocolConsts::WEB_PARAM_RIGHTS => $user->Rights,
            MTProtocolConsts::WEB_PARAM_GROUP => $user->Group,
            MTProtocolConsts::WEB_PARAM_NAME => $user->Name,
            MTProtocolConsts::WEB_PARAM_COMPANY => $user->Company,
            MTProtocolConsts::WEB_PARAM_LANGUAGE => $user->Language,
            MTProtocolConsts::WEB_PARAM_COUNTRY => $user->Country,
            MTProtocolConsts::WEB_PARAM_CITY => $user->City,
            MTProtocolConsts::WEB_PARAM_STATE => $user->State,
            MTProtocolConsts::WEB_PARAM_ZIPCODE => $user->ZipCode,
            MTProtocolConsts::WEB_PARAM_ADDRESS => $user->Address,
            MTProtocolConsts::WEB_PARAM_PHONE => $user->Phone,
            MTProtocolConsts::WEB_PARAM_EMAIL => $user->Email,
            MTProtocolConsts::WEB_PARAM_ID => $user->ID,
            MTProtocolConsts::WEB_PARAM_STATUS => $user->Status,
            MTProtocolConsts::WEB_PARAM_COMMENT => $user->Comment,
            MTProtocolConsts::WEB_PARAM_COLOR => $user->Color,
            MTProtocolConsts::WEB_PARAM_PASS_PHONE => $user->PhonePassword,
            MTProtocolConsts::WEB_PARAM_LEVERAGE => $user->Leverage,
            MTProtocolConsts::WEB_PARAM_AGENT => $user->Agent,
            MTProtocolConsts::WEB_PARAM_BALANCE => $user->Balance,
            MTProtocolConsts::WEB_PARAM_BODYTEXT => MTJson::Encode($user)
        );
    }

    /**
     * Get array of params for sending to MetaTrader 5 server
     *
     * @param MTUser $user
     *
     * @return array
     */
    private function GetParamUpdate($user)
    {
        return array(
            MTProtocolConsts::WEB_PARAM_LOGIN => $user->Login,
            MTProtocolConsts::WEB_PARAM_RIGHTS => $user->Rights,
            MTProtocolConsts::WEB_PARAM_GROUP => $user->Group,
            MTProtocolConsts::WEB_PARAM_NAME => $user->Name,
            MTProtocolConsts::WEB_PARAM_COMPANY => $user->Company,
            MTProtocolConsts::WEB_PARAM_LANGUAGE => $user->Language,
            MTProtocolConsts::WEB_PARAM_COUNTRY => $user->Country,
            MTProtocolConsts::WEB_PARAM_CITY => $user->City,
            MTProtocolConsts::WEB_PARAM_STATE => $user->State,
            MTProtocolConsts::WEB_PARAM_ZIPCODE => $user->ZipCode,
            MTProtocolConsts::WEB_PARAM_ADDRESS => $user->Address,
            MTProtocolConsts::WEB_PARAM_PHONE => $user->Phone,
            MTProtocolConsts::WEB_PARAM_EMAIL => $user->Email,
            MTProtocolConsts::WEB_PARAM_ID => $user->ID,
            MTProtocolConsts::WEB_PARAM_STATUS => $user->Status,
            MTProtocolConsts::WEB_PARAM_COMMENT => $user->Comment,
            MTProtocolConsts::WEB_PARAM_COLOR => $user->Color,
            MTProtocolConsts::WEB_PARAM_PASS_PHONE => $user->PhonePassword,
            MTProtocolConsts::WEB_PARAM_LEVERAGE => $user->Leverage,
            MTProtocolConsts::WEB_PARAM_AGENT => $user->Agent,
            MTProtocolConsts::WEB_PARAM_BODYTEXT => MTJson::Encode($user)
        );
    }
}
