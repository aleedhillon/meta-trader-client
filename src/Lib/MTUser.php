<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * User record
 */
class MTUser
{
    const EXTERNAL_ID_MAXLEN = 32;
    const EXTERNAL_ID_LIMIT = 128;
    //--- login
    public $Login;
    //--- group
    public $Group;
    //--- certificate serial number
    public $CertSerialNumber;
    //--- MTEnUsersRights
    public $Rights;
    //--- client's MetaQuotes ID
    public $MQID;
    //--- registration datetime (filled by MT5)
    public $Registration;
    //--- last access datetime (filled by MT5)
    public $LastAccess;
    //--- last password change datetime (filled by MT5)
    public $LastPassChange;
    //--- last ip-address
    public $LastIP;
    //--- name
    public $Name;
    //--- company
    public $Company;
    //--- external system account (exchange, ECN, etc)
    public $Account;
    //--- country
    public $Country;
    //--- client language (WinAPI LANGID)
    public $Language;
    //--- identificator by client
    public $ClientID;
    //--- city
    public $City;
    //--- state
    public $State;
    //--- ZIP code
    public $ZipCode;
    //--- address
    public $Address;
    //--- phone
    public $Phone;
    //--- email
    public $Email;
    //--- additional ID
    public $ID;
    //--- additional status
    public $Status;
    //--- comment
    public $Comment;
    //--- color
    public $Color;
    //--- phone password
    public $PhonePassword;
    //--- leverage
    public $Leverage;
    //--- agent account
    public $Agent;
    //--- balance & credit
    public $Balance;
    public $Credit;
    //--- accumulated interest rate
    public $InterestRate;
    //--- accumulated daily and monthly commissions
    public $CommissionDaily;
    public $CommissionMonthly;
    //--- previous balance state
    public $BalancePrevDay;
    public $BalancePrevMonth;
    //--- previous equity state
    public $EquityPrevDay;
    //--- previous equity state month
    public $EquityPrevMonth;
    //--- external trade accounts
    public $TradeAccounts;
    //--- lead campaign
    public $LeadCampaign;
    //--- lead source
    public $LeadSource;
    //--- main password
    public $MainPassword;
    //--- invest password
    public $InvestPassword;

    /**
     * Create user with default values
     * @return MTUser
     */
    public static function CreateDefault()
    {
        $user = new MTUser();
        //---
        $user->Rights = 0x1E3; // MTEnUsersRights::USER_RIGHT_ENABLED | MTEnUsersRights::USER_RIGHT_PASSWORD | MTEnUsersRights::USER_RIGHT_TRAILING | MTEnUsersRights::USER_RIGHT_EXPERT | MTEnUsersRights::USER_RIGHT_API | MTEnUsersRights::USER_RIGHT_REPORTS
        $user->Leverage = 100;
        $user->Color = 0xFF000000;
        //---
        return $user;
    }

    /**
     * Add external account to trade account
     * @param int $gateway_id
     * @param string $account
     * @return MTRetCode
     */
    public function ExternalAccountAdd($gateway_id, $account)
    {
        //--- checks
        if ($account == "")
            return MTRetCode::MT_RET_ERR_PARAMS;
        if (strlen($account) >= self::EXTERNAL_ID_MAXLEN)
            return MTRetCode::MT_RET_ERR_DATA;
        //--- add new account
        $tmp = sprintf("%u=%s|", $gateway_id, $account);
        $result = $this->TradeAccounts . $tmp;
        //--- checks and update
        if (self::EXTERNAL_ID_LIMIT <= strlen($result))
            return MTRetCode::MT_RET_ERR_DATA;
        $this->TradeAccounts = $result;
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Update external account to trade account
     * @param int $pos
     * @param int $gateway_id
     * @param string $account
     * @return MTRetCode
     */
    public function ExternalAccountUpdate($pos, $gateway_id, $account)
    {
        //--- checks
        if ($account == "")
            return MTRetCode::MT_RET_ERR_PARAMS;
        if (strlen($account) >= self::EXTERNAL_ID_MAXLEN)
            return MTRetCode::MT_RET_ERR_DATA;
        //--- update
        $tokens = explode("|", $this->TradeAccounts);
        $count = 0;
        $result = "";
        foreach ($tokens as $token) {
            if (strlen($token) < 1)
                continue;
            if ($pos == $count) {
                $tmp = sprintf("%u=%s|", $gateway_id, $account);
                $result = $result . $tmp;
            } else {
                $result = $result . $token;
                $result = $result . "|";
            }
            $count++;
        }
        //--- checks and update
        if ($pos >= $count)
            return MTRetCode::MT_RET_ERR_PARAMS;
        if (self::EXTERNAL_ID_LIMIT <= strlen($result))
            return MTRetCode::MT_RET_ERR_DATA;
        $this->TradeAccounts = $result;
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Update external account to trade account
     * @param int $pos
     * @return MTRetCode
     */
    public function ExternalAccountDelete($pos)
    {
        //--- delete
        $tokens = explode("|", $this->TradeAccounts);
        $count = 0;
        $result = "";
        foreach ($tokens as $token) {
            if (strlen($token) < 1)
                continue;
            if ($pos != $count) {
                $result = $result . $token;
                $result = $result . "|";
            }
            $count++;
        }
        //--- checks and delete
        if ($pos >= $count)
            return MTRetCode::MT_RET_ERR_PARAMS;
        if (self::EXTERNAL_ID_LIMIT <= strlen($result))
            return MTRetCode::MT_RET_ERR_DATA;
        $this->TradeAccounts = $result;
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Clear all external accounts
     * @return MTRetCode
     */
    public function ExternalAccountClear()
    {
        $this->TradeAccounts = "";
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Total count of external accounts
     * @return int
     */
    public function ExternalAccountTotal()
    {
        $tokens = explode("|", $this->TradeAccounts);
        $count = 0;
        foreach ($tokens as $token) {
            if (strlen($token) < 1)
                continue;
            $count++;
        }
        return $count;
    }

    /**
     * Get external account by position
     * @param int $pos
     * @param int $gateway_id
     * @param string $account
     * @return MTRetCode
     */
    public function ExternalAccountNext($pos, &$gateway_id, &$account)
    {
        $gateway_id = 0;
        $account = "";
        $tokens = explode("|", $this->TradeAccounts);
        $count = 0;
        foreach ($tokens as $token) {
            if (strlen($token) < 1)
                continue;
            if ($pos == $count) {
                list($gateway_id, $account) = explode("=", $token);
                return MTRetCode::MT_RET_OK;
            }
            $count++;
        }
        return MTRetCode::MT_RET_ERR_PARAMS;
    }

    /**
     * Find external account for gateway
     * @param int $gateway_id
     * @param string $account
     * @return MTRetCode
     */
    public function ExternalAccountGet($gateway_id, &$account)
    {
        $account = "";
        $tokens = explode("|", $this->TradeAccounts);
        foreach ($tokens as $token) {
            if (strlen($token) < 1)
                continue;
            list($tmp_gateway_id, $tmp_account) = explode("=", $token);
            if ($tmp_gateway_id == $gateway_id) {
                $account = $tmp_account;
                return MTRetCode::MT_RET_OK;
            }
        }
        return MTRetCode::MT_RET_ERR_NOTFOUND;
    }

}
