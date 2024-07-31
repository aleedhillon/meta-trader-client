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
    private $m_connect; // connection to MT5 server
    /**
     * @param $connect MTConnect connect to MT5 server
     */
    public function __construct($connect)
    {
        $this->m_connect = $connect;
    }

    /**
     * Add new user
     *
     * @param $user     MTUser information about user
     * @param $new_user MTUser information about user getting from server
     *
     * @return MTRetCode
     */
    public function Add($user, &$new_user)
    {
        //--- send request
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_USER_ADD, $this->GetParamAdd($user))) {
            if (MTLogger::getIsWriteLog()) if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send user add failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer user add is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseAddUser($answer, $user_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse user add failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //---
        $new_user = $user_answer->GetFromJson();
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Check answer from MetaTrader 5 server
     *
     * @param  $answer      string answer from server
     * @param  $user_answer MTUserAnswer
     *
     * @return MTRetCode
     */
    private function ParseAddUser(&$answer, &$user_answer)
    {
        $pos = 0;
        //--- get command answer
        $command_real = $this->m_connect->GetCommand($answer, $pos);
        if ($command_real != MTProtocolConsts::WEB_CMD_USER_ADD)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $user_answer = new MTUserAnswer();
        //--- get param
        $pos_end = -1;
        while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $user_answer->RetCode = $param['value'];
                    break;
                //---
                case MTProtocolConsts::WEB_PARAM_LOGIN:
                    $user_answer->Login = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($ret_code = MTConnect::GetRetCode($user_answer->RetCode)) != MTRetCode::MT_RET_OK)
            return $ret_code;
        //--- check login
        if (empty($user_answer->Login))
            return MTRetCode::MT_RET_ERR_PARAMS;
        //--- get json
        if (($user_answer->ConfigJson = $this->m_connect->GetJson($answer, $pos_end)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Check answer from MetaTrader 5 server
     *
     * @param  $command     string command
     * @param  $answer      string answer from server
     * @param  $user_answer MTUserAnswer
     *
     * @return MTRetCode
     */
    private function ParseUser($command, &$answer, &$user_answer)
    {
        $pos = 0;
        //--- get command answer
        $command_real = $this->m_connect->GetCommand($answer, $pos);
        if ($command_real != $command)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $user_answer = new MTUserAnswer();
        //--- get param
        $pos_end = -1;
        while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $user_answer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($ret_code = MTConnect::GetRetCode($user_answer->RetCode)) != MTRetCode::MT_RET_OK)
            return $ret_code;
        //--- get json
        if (($user_answer->ConfigJson = $this->m_connect->GetJson($answer, $pos_end)) == null)
            return MTRetCode::MT_RET_REPORT_NODATA;
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Update user
     *
     * @param $user     MTUser information about user
     * @param $new_user MTUser information about user getting from server
     *
     * @return MTRetCode
     */
    public function Update($user, &$new_user)
    {
        //--- send request
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_USER_UPDATE, $this->GetParamUpdate($user))) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send user update failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer user update is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseUser(MTProtocolConsts::WEB_CMD_USER_UPDATE, $answer, $user_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse user add failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //---
        $new_user = $user_answer->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Update user
     *
     * @param $login int login
     * @param $user  MTUser information about user getting from server
     *
     * @return MTRetCode
     */
    public function Get($login, &$user)
    {
        $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login);
        //--- send request
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_USER_GET, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send user get failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer user get is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseUser(MTProtocolConsts::WEB_CMD_USER_GET, $answer, $user_answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse user get failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //---
        $user = $user_answer->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Update user
     *
     * @param $login int login
     *
     * @return MTRetCode
     */
    public function Delete($login)
    {
        $login = (int) $login;
        $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login);
        //--- send request
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_USER_DELETE, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send user delete failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer user delete is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseClearCommand(MTProtocolConsts::WEB_CMD_USER_DELETE, $answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse user delete failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
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
     * @return MTRetCode
     */
    private function ParseClearCommand($command, &$answer)
    {
        $pos = 0;
        //--- get command answer
        $command_real = $this->m_connect->GetCommand($answer, $pos);
        if ($command_real != $command)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $user_answer = new MTUserAnswer();
        //--- get param
        $pos_end = -1;
        while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $user_answer->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($ret_code = MTConnect::GetRetCode($user_answer->RetCode)) != MTRetCode::MT_RET_OK)
            return $ret_code;
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
     * @return MTRetCode
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
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_USER_PASS_CHECK, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send user password check failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer user password check is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseClearCommand(MTProtocolConsts::WEB_CMD_USER_PASS_CHECK, $answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse user password check failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * user password change
     *
     * @param        $login        int
     * @param        $new_password string new password
     * @param string $type         WEB_VAL_USER_PASS_MAIN | WEB_VAL_USER_PASS_INVESTOR
     *
     * @return MTRetCode
     */
    public function PasswordChange($login, $new_password, $type = MTProtocolConsts::WEB_VAL_USER_PASS_MAIN)
    {
        $login = (int) $login;
        //--- send request
        $data = array(
            MTProtocolConsts::WEB_PARAM_LOGIN => $login,
            MTProtocolConsts::WEB_PARAM_TYPE => $type,
            MTProtocolConsts::WEB_PARAM_PASSWORD => $new_password
        );
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_USER_PASS_CHANGE, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send user password change failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer user password change is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseClearCommand(MTProtocolConsts::WEB_CMD_USER_PASS_CHANGE, $answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse user password change failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * user deposit change
     *
     * @param $login       int
     * @param $new_deposit float deposit
     * @param $comment     string comment
     * @param $type        MTEnDealAction type of balance: DEAL_BALANCE, DEAL_CREDIT, DEAL_CHARGE, DEAL_BONUS
     *
     * @return MTRetCode
     */
    public function DepositChange($login, $new_deposit, $comment, $type)
    {
        $login = (int) $login;
        //--- send request
        $data = array(
            MTProtocolConsts::WEB_PARAM_LOGIN => $login,
            MTProtocolConsts::WEB_PARAM_TYPE => $type,
            MTProtocolConsts::WEB_PARAM_BALANCE => $new_deposit,
            MTProtocolConsts::WEB_PARAM_COMMENT => $comment
        );
        //--
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_USER_DEPOSIT_CHANGE, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send user deposit change failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer user deposit change is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseClearCommand(MTProtocolConsts::WEB_CMD_USER_DEPOSIT_CHANGE, $answer)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse user deposit change failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
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
     * @return MTRetCode
     */
    public function AccountGet($login, &$account)
    {
        $login = (int) $login;
        $data = array(MTProtocolConsts::WEB_PARAM_LOGIN => $login);
        //--- send request
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_USER_ACCOUNT_GET, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send user account get failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer user account get is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- parse answer
        if (($error_code = $this->ParseUserAccount(MTProtocolConsts::WEB_CMD_USER_ACCOUNT_GET, $answer, $user_account)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse user account get failed: [' . $error_code . ']' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //---
        $account = $user_account->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * parsing answer for command user account get
     *
     * @param $command      MTProtocolConsts
     * @param $answer       string
     * @param $user_account MTUserAccountAnswer
     *
     * @return MTRetCode
     */
    private function ParseUserAccount($command, $answer, &$user_account)
    {
        $pos = 0;
        //--- get command answer
        $command_real = $this->m_connect->GetCommand($answer, $pos);
        if ($command_real != $command)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $user_account = new MTUserAccountAnswer();
        //--- get param
        $pos_end = -1;
        while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $user_account->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($ret_code = MTConnect::GetRetCode($user_account->RetCode)) != MTRetCode::MT_RET_OK)
            return $ret_code;
        //--- get json
        if (($user_account->ConfigJson = $this->m_connect->GetJson($answer, $pos_end)) == null)
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
     * @return MTRetCode
     */
    public function UserLogins($group, &$logins)
    {
        $data = array(MTProtocolConsts::WEB_PARAM_GROUP => $group);
        //--- send request
        if (!$this->m_connect->Send(MTProtocolConsts::WEB_CMD_USER_USER_LOGINS, $data)) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'send user logins get failed');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- get answer
        if (($answer = $this->m_connect->Read()) == null) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'answer user logins get is empty');
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        $user_logins = null;
        //--- parse answer
        if (($error_code = $this->ParseUserLogins($answer, $user_logins)) != MTRetCode::MT_RET_OK) {
            if (MTLogger::getIsWriteLog())
                MTLogger::write(MTLoggerType::ERROR, 'parse user logins get failed: [' . $error_code . '] ' . MTRetCode::GetError($error_code));
            return $error_code;
        }
        //---
        $logins = $user_logins->GetFromJson();
        //---
        return MTRetCode::MT_RET_OK;
    }

    /**
     * parsing answer for command user_logins
     *
     * @param $answer       string
     * @param $user_account MTUserAccountAnswer
     *
     * @return MTRetCode
     */
    private function ParseUserLogins($answer, &$user_account)
    {
        $pos = 0;
        //--- get command answer
        $command_real = $this->m_connect->GetCommand($answer, $pos);
        if ($command_real != MTProtocolConsts::WEB_CMD_USER_USER_LOGINS)
            return MTRetCode::MT_RET_ERR_DATA;
        //---
        $user_account = new MTUserLoginsAnswer();
        //--- get param
        $pos_end = -1;
        while (($param = $this->m_connect->GetNextParam($answer, $pos, $pos_end)) != null) {
            switch ($param['name']) {
                case MTProtocolConsts::WEB_PARAM_RETCODE:
                    $user_account->RetCode = $param['value'];
                    break;
            }
        }
        //--- check ret code
        if (($ret_code = MTConnect::GetRetCode($user_account->RetCode)) != MTRetCode::MT_RET_OK)
            return $ret_code;
        //--- get json
        if (($user_account->ConfigJson = $this->m_connect->GetJson($answer, $pos_end)) == null)
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
     * Get string of params for sending to MetaTrader 5 server
     *
     * @param $user MTUser
     *
     * @return string
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
     * Get string of params for sending to MetaTrader 5 server
     *
     * @param MTUser $user
     *
     * @return string
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
