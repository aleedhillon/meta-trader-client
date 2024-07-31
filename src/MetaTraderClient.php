<?php

namespace Aleedhillon\MetaTraderClient;

use Aleedhillon\MetaTraderClient\Lib\MTDeal;
use Aleedhillon\MetaTraderClient\Lib\MTUser;
use Aleedhillon\MetaTraderClient\Lib\MTOrder;
use Aleedhillon\MetaTraderClient\Lib\MTLogger;
use Aleedhillon\MetaTraderClient\Lib\MTServer;
use Aleedhillon\MetaTraderClient\Lib\MTAccount;
use Aleedhillon\MetaTraderClient\Lib\MTConnect;
use Aleedhillon\MetaTraderClient\Lib\MTConTime;
use Aleedhillon\MetaTraderClient\Lib\MTRetCode;
use Aleedhillon\MetaTraderClient\Lib\MTConGroup;
use Aleedhillon\MetaTraderClient\Lib\MTPosition;
use Aleedhillon\MetaTraderClient\Lib\MTConCommon;
use Aleedhillon\MetaTraderClient\Lib\MTConSymbol;
use Aleedhillon\MetaTraderClient\Lib\MTLoggerType;
use Aleedhillon\MetaTraderClient\Lib\MTAuthProtocol;
use Aleedhillon\MetaTraderClient\Lib\MTDealProtocol;
use Aleedhillon\MetaTraderClient\Lib\MTEnDealAction;
use Aleedhillon\MetaTraderClient\Lib\MTMailProtocol;
use Aleedhillon\MetaTraderClient\Lib\MTNewsProtocol;
use Aleedhillon\MetaTraderClient\Lib\MTPingProtocol;
use Aleedhillon\MetaTraderClient\Lib\MTTickProtocol;
use Aleedhillon\MetaTraderClient\Lib\MTTimeProtocol;
use Aleedhillon\MetaTraderClient\Lib\MTUserProtocol;
use Aleedhillon\MetaTraderClient\Lib\MTGroupProtocol;
use Aleedhillon\MetaTraderClient\Lib\MTOrderProtocol;
use Aleedhillon\MetaTraderClient\Lib\MTTradeProtocol;
use Aleedhillon\MetaTraderClient\Lib\MTCommonProtocol;
use Aleedhillon\MetaTraderClient\Lib\MTCustomProtocol;
use Aleedhillon\MetaTraderClient\Lib\MTProtocolConsts;
use Aleedhillon\MetaTraderClient\Lib\MTSymbolProtocol;
use Aleedhillon\MetaTraderClient\Lib\MTHistoryProtocol;
use Aleedhillon\MetaTraderClient\Lib\MTPositionProtocol;

//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2000-2021, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
//--- web api version
define("WebAPIVersion", 4153);
//--- web api date
define("WebAPIDate", "22 Jan 2024");

class MetaTraderClient
{
    private ?MTConnect $connector = null;
    private string $agent;
    private bool $shouldCrypt;

    private string $ip;
    private int $port;
    private int $timeout;
    private string $login;
    private string $password;

    public function __construct(
        $agent = 'WebAPI',
        $file_path = null,
        $shouldCrypt = true,
        $ip = null,
        $port = null,
        $timeout = null,
        $login = null,
        $password = null
    ) {
        $this->agent = $agent;
        $this->shouldCrypt = $shouldCrypt;

        $this->ip = $ip ?? config('meta-trader-client.ip');
        $this->port = $port ?? config('meta-trader-client.port');
        $this->timeout = $timeout ?? config('meta-trader-client.timeout');
        $this->login = $login ?? config('meta-trader-client.login');
        $this->password = $password ?? config('meta-trader-client.password');

        MTLogger::Init($agent, true, $file_path);
    }

    public function connect(): int|MTRetCode
    {
        //--- create connection class
        $this->connector = new MTConnect($this->ip, $this->port, $this->timeout, $this->shouldCrypt);
        //--- create connection
        $connectionResponseCode = $this->connector->Connect();

        if ($connectionResponseCode != MTRetCode::MT_RET_OK) {
            return $connectionResponseCode;
        }
        //--- authorization to MetaTrader 5 server
        $authenticator = new MTAuthProtocol($this->connector, $this->agent);
        //---
        $crypt_rand = '';

        $authRespondeCode = $authenticator->Auth($this->login, $this->password, $this->shouldCrypt, $crypt_rand);
        if ($authRespondeCode != MTRetCode::MT_RET_OK) {
            //--- disconnect
            $this->Disconnect();
            return $authRespondeCode;
        }
        //--- if need crypt
        if ($this->shouldCrypt)
            $this->connector->SetCryptRand($crypt_rand, $this->password);
        //---
        return MTRetCode::MT_RET_OK;
    }

    public function isConnected(): bool
    {
        return $this->connector != null;
    }

    public function disconnect(): void
    {
        if ($this->connector) {
            $this->connector->Disconnect();
            $this->connector = null;
        }
    }

    public function connectIfNotConnected(): int|MTRetCode
    {
        if (!$this->isConnected()) {
            return $this->connect();
        }

        return MTRetCode::MT_RET_OK;
    }

    /**
     * Get current time from server
     *
     * @param MTConTime $time - time
     *
     * @return MTRetCode
     */
    public function TimeGet(&$time)
    {
        $this->connectIfNotConnected();

        $mt_time = new MTTimeProtocol($this->connector);
        return $mt_time->TimeGet($time);
    }

    /**
     * Get current time from server
     * @return int - time in unix format
     */
    public function TimeServer()
    {
        $this->connectIfNotConnected();

        $mt_time = new MTTimeProtocol($this->connector);
        return $mt_time->TimeServer();
    }

    /**
     * Get common information
     *
     * @param MTConCommon $common
     *
     * @return MTRetCode
     */
    public function CommonGet(&$common)
    {
        $this->connectIfNotConnected();

        $mt_common = new MTCommonProtocol($this->connector);
        return $mt_common->CommonGet($common);
    }

    /**
     * Get count of groups
     *
     * @param int $total - count groups
     *
     * @return MTRetCode
     */
    public function GroupTotal(&$total)
    {
        $this->connectIfNotConnected();

        $mt_group = new MTGroupProtocol($this->connector);
        return $mt_group->GroupTotal($total);
    }

    /**
     * Get next group
     *
     * @param int        $pos        - position
     * @param MTConGroup $group_next - next group
     *
     * @return MTRetCode
     */
    public function GroupNext($pos, &$group_next)
    {
        $this->connectIfNotConnected();

        $mt_group = new MTGroupProtocol($this->connector);
        return $mt_group->GroupNext($pos, $group_next);
    }

    /**
     * Get group by name
     *
     * @param string     $name - name group
     * @param MTConGroup $group
     *
     * @return MTRetCode
     */
    public function GroupGet($name, &$group)
    {
        $this->connectIfNotConnected();

        $mt_group = new MTGroupProtocol($this->connector);
        return $mt_group->GroupGet($name, $group);
    }

    /**
     * Add or update group
     *
     * @param MTConGroup $group
     * @param MTConGroup $new_group
     *
     * @return MTRetCode
     */
    public function GroupAdd($group, &$new_group)
    {
        $this->connectIfNotConnected();

        $mt_group = new MTGroupProtocol($this->connector);
        return $mt_group->GroupAdd($group, $new_group);
    }

    /**
     * Delete group by name
     *
     * @param string     $name - name group
     *
     * @return MTRetCode
     */
    public function GroupDelete($name)
    {
        $this->connectIfNotConnected();

        $mt_group = new MTGroupProtocol($this->connector);
        return $mt_group->GroupDelete($name);
    }

    /**
     * Get count symbols
     *
     * @param int $total - get total symbols
     *
     * @return MTRetCode
     */
    public function SymbolTotal(&$total)
    {
        $this->connectIfNotConnected();

        $symbol = new MTSymbolProtocol($this->connector);
        return $symbol->SymbolTotal($total);
    }

    /**
     * Get next symbol
     *
     * @param int         $pos
     * @param MTConSymbol $symbol_next
     *
     * @return MTRetCode
     */
    public function SymbolNext($pos, &$symbol_next)
    {
        $this->connectIfNotConnected();

        $mt_symbol = new MTSymbolProtocol($this->connector);
        return $mt_symbol->SymbolNext($pos, $symbol_next);
    }

    /**
     * Get symbol
     *
     * @param string      $name
     * @param MTConSymbol $symbol
     *
     * @return MTRetCode
     */
    public function SymbolGet($name, &$symbol)
    {
        $this->connectIfNotConnected();

        $mt_symbol = new MTSymbolProtocol($this->connector);
        return $mt_symbol->SymbolGet($name, $symbol);
    }

    /**
     * Get config symbol
     *
     * @param string      $name  - symbol name
     * @param string      $group - group name
     * @param MTConSymbol $symbol
     *
     * @return MTRetCode
     */
    public function SymbolGetGroup($name, $group, &$symbol)
    {
        $this->connectIfNotConnected();

        $mt_symbol = new MTSymbolProtocol($this->connector);
        return $mt_symbol->SymbolGetGroup($name, $group, $symbol);
    }

    /**
     * Symbol add and update
     *
     * @param MTConSymbol     $symbol     - symbol need add
     * @param MTConSymbol     $new_symbol - symbol added to server
     *
     * @return MTRetCode
     */
    public function SymbolAdd($symbol, &$new_symbol)
    {
        $this->connectIfNotConnected();

        $mt_symbol = new MTSymbolProtocol($this->connector);
        return $mt_symbol->SymbolAdd($symbol, $new_symbol);
    }

    /**
     * Symbol delete
     *
     * @param string $name
     *
     * @return MTRetCode
     */
    public function SymbolDelete($name)
    {
        $this->connectIfNotConnected();

        $mt_symbol = new MTSymbolProtocol($this->connector);
        return $mt_symbol->SymbolDelete($name);
    }

    /**
     * Add user to server
     *
     * @param MTUser $user     - user add to server
     * @param MTUser $new_user - user added to server
     *
     * @return MTRetCode
     */
    public function UserAdd($user, &$new_user)
    {
        $this->connectIfNotConnected();

        $mt_user = new MTUserProtocol($this->connector);
        return $mt_user->Add($user, $new_user);
    }

    /**
     * Update user to server
     *
     * @param MTUser $user - user add to server
     * @param MTUser $new_user
     *
     * @return MTRetCode
     */
    public function UserUpdate($user, &$new_user)
    {
        $this->connectIfNotConnected();

        $mt_user = new MTUserProtocol($this->connector);
        return $mt_user->Update($user, $new_user);
    }

    /**
     * User delete from server
     *
     * @param int $login
     *
     * @return MTRetCode
     */
    public function UserDelete($login)
    {
        $this->connectIfNotConnected();

        $mt_user = new MTUserProtocol($this->connector);
        return $mt_user->Delete($login);
    }

    /**
     * Get user
     *
     * @param int    $login
     * @param MTUser $user
     *
     * @return MTRetCode
     */
    public function UserGet($login, &$user)
    {
        $this->connectIfNotConnected();

        $mt_user = new MTUserProtocol($this->connector);
        return $mt_user->Get($login, $user);
    }

    /**
     * Check login and password
     *
     * @param int    $login
     * @param string $password
     * @param string $type
     *
     * @return MTRetCode
     */
    public function UserPasswordCheck($login, $password, $type = MTProtocolConsts::WEB_VAL_USER_PASS_MAIN)
    {
        $this->connectIfNotConnected();

        $mt_user = new MTUserProtocol($this->connector);
        return $mt_user->PasswordCheck($login, $password, $type);
    }

    /**
     * User change password
     *
     * @param int    $login
     * @param string $new_password - new password
     * @param string $type
     *
     * @return MTRetCode
     */
    public function UserPasswordChange($login, $new_password, $type = MTProtocolConsts::WEB_VAL_USER_PASS_MAIN)
    {
        $this->connectIfNotConnected();

        $mt_user = new MTUserProtocol($this->connector);
        return $mt_user->PasswordChange($login, $new_password, $type);
    }

    /**
     * User deposit change
     *
     * @param int            $login
     * @param float          $new_deposit - new deposit
     * @param string         $comment     - comment
     * @param MTEnDealAction $type
     *
     * @return MTRetCode
     */
    public function UserDepositChange($login, $new_deposit, $comment, $type)
    {
        $this->connectIfNotConnected();

        $mt_user = new MTUserProtocol($this->connector);
        return $mt_user->DepositChange($login, $new_deposit, $comment, $type);
    }

    /**
     * Get account information
     *
     * @param int       $login
     * @param MTAccount $account
     *
     * @return MTRetCode
     */
    public function UserAccountGet($login, &$account)
    {
        $this->connectIfNotConnected();

        $mt_user = new MTUserProtocol($this->connector);
        return $mt_user->AccountGet($login, $account);
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
        $this->connectIfNotConnected();

        $mt_user = new MTUserProtocol($this->connector);
        return $mt_user->UserLogins($group, $logins);
    }

    /**
     * Get order
     *
     * @param int     $ticket
     * @param MTOrder $order
     *
     * @return MTRetCode
     */
    public function OrderGet($ticket, &$order)
    {
        $this->connectIfNotConnected();

        $mt_order = new MTOrderProtocol($this->connector);
        return $mt_order->OrderGet($ticket, $order);
    }

    /**
     * Get all user orders
     *
     * @param int $login - user login
     * @param int $total - count of orders
     *
     * @return MTRetCode
     */
    public function OrderGetTotal($login, &$total)
    {
        $this->connectIfNotConnected();

        $mt_order = new MTOrderProtocol($this->connector);
        return $mt_order->OrderGetTotal($login, $total);
    }

    /**
     * Get orders by page
     *
     * @param int            $login  - user login
     * @param int            $offset - record begin
     * @param int            $total  - count needs orders
     * @param array(MTOrder) $orders
     *
     * @return MTRetCode
     */
    public function OrderGetPage($login, $offset, $total, &$orders)
    {
        $this->connectIfNotConnected();

        $mt_order = new MTOrderProtocol($this->connector);
        return $mt_order->OrderGetPage($login, $offset, $total, $orders);
    }

    /**
     * Get position
     *
     * @param int        $login
     * @param string     $symbol
     * @param MTPosition $position
     *
     * @return MTRetCode
     */
    public function PositionGet($login, $symbol, &$position)
    {
        $this->connectIfNotConnected();

        $mt_position = new MTPositionProtocol($this->connector);
        return $mt_position->PositionGet($login, $symbol, $position);
    }

    /**
     * Get all user positions
     *
     * @param int $login - user login
     * @param int $total - count of positions
     *
     * @return MTRetCode
     */
    public function PositionGetTotal($login, &$total)
    {
        $this->connectIfNotConnected();

        $mt_position = new MTPositionProtocol($this->connector);
        return $mt_position->PositionGetTotal($login, $total);
    }

    /**
     * Get positions by page
     *
     * @param int               $login  - user login
     * @param int               $offset - record begin
     * @param int               $total  - count needs orders
     * @param array(MTPosition) $positions
     *
     * @return MTRetCode
     */
    public function PositionGetPage($login, $offset, $total, &$positions)
    {
        $this->connectIfNotConnected();

        $mt_position = new MTPositionProtocol($this->connector);
        return $mt_position->PositionGetPage($login, $offset, $total, $positions);
    }

    /**
     * Get deal
     *
     * @param int    $ticket
     * @param MTDeal $deal
     *
     * @return MTRetCode
     */
    public function DealGet($ticket, &$deal)
    {
        $this->connectIfNotConnected();

        $mt_deal = new MTDealProtocol($this->connector);
        return $mt_deal->DealGet($ticket, $deal);
    }

    /**
     * Get count deals
     *
     * @param int $login - user login
     * @param int $from  - from date
     * @param int $to    - to date
     * @param int $total - count of deals
     *
     * @return MTRetCode
     */
    public function DealGetTotal($login, $from, $to, &$total)
    {
        $this->connectIfNotConnected();

        $mt_deal = new MTDealProtocol($this->connector);
        return $mt_deal->DealGetTotal($login, $from, $to, $total);
    }

    /**
     * Get orders by page
     *
     * @param int           $login  - user login
     * @param int           $from   - from date
     * @param int           $to     - to date
     * @param int           $offset - record begin
     * @param int           $total  - count needs orders
     * @param array(MTDeal) $deals
     *
     * @return MTRetCode
     */
    public function DealGetPage($login, $from, $to, $offset, $total, &$deals)
    {
        $this->connectIfNotConnected();

        $mt_deal = new MTDealProtocol($this->connector);
        return $mt_deal->DealGetPage($login, $from, $to, $offset, $total, $deals);
    }

    /**
     * Get history
     *
     * @param int     $ticket
     * @param MTOrder $history
     *
     * @return MTRetCode
     */
    public function HistoryGet($ticket, &$history)
    {
        $this->connectIfNotConnected();

        $mt_deal = new MTHistoryProtocol($this->connector);
        return $mt_deal->HistoryGet($ticket, $history);
    }

    /**
     * Get count deals
     *
     * @param int $login - user login
     * @param int $from  - from date
     * @param int $to    - to date
     * @param int $total - count of history
     *
     * @return MTRetCode
     */
    public function HistoryGetTotal($login, $from, $to, &$total)
    {
        $this->connectIfNotConnected();

        $mt_deal = new MTHistoryProtocol($this->connector);
        return $mt_deal->HistoryGetTotal($login, $from, $to, $total);
    }

    /**
     * Get orders by page
     *
     * @param int            $login  - user login
     * @param int            $from   - from date
     * @param int            $to     - to date
     * @param int            $offset - record begin
     * @param int            $total  - count needs orders
     * @param array(MTOrder) $orders
     *
     * @return MTRetCode
     */
    public function HistoryGetPage($login, $from, $to, $offset, $total, &$orders)
    {
        $this->connectIfNotConnected();

        $mt_deal = new MTHistoryProtocol($this->connector);
        return $mt_deal->HistoryGetPage($login, $from, $to, $offset, $total, $orders);
    }

    /**
     * Get last tickets
     *
     * @param string        $symbol
     * @param array(MTTick) $ticks
     *
     * @return MTRetCode
     */
    public function TickLast($symbol, &$ticks)
    {
        $this->connectIfNotConnected();

        $mt_tick = new MTTickProtocol($this->connector);
        return $mt_tick->TickLast($symbol, $ticks);
    }

    /**
     * Get last tickets by symbol and group
     *
     * @param string        $symbol
     * @param string        $group
     * @param array(MTTick) $ticks
     *
     * @return MTRetCode
     */
    public function TickLastGroup($symbol, $group, &$ticks)
    {
        $this->connectIfNotConnected();

        $mt_tick = new MTTickProtocol($this->connector);
        return $mt_tick->TickLastGroup($symbol, $group, $ticks);
    }

    /**
     * Get last tickets
     *
     * @param string            $symbol
     * @param array(MTTickStat) $tick_stat
     *
     * @return MTRetCode
     */
    public function TickStat($symbol, &$tick_stat)
    {
        $this->connectIfNotConnected();

        $mt_tick = new MTTickProtocol($this->connector);
        return $mt_tick->TickStat($symbol, $tick_stat);
    }

    /**
     * Send mail to user
     *
     * @param string $to      - user login or mask
     * @param string $subject - subject of mail
     * @param string $text    - mail text, may be in html format
     *
     * @return MTRetCode
     */
    public function MailSend($to, $subject, $text)
    {
        $this->connectIfNotConnected();

        $mt_mail = new MTMailProtocol($this->connector);
        return $mt_mail->MailSend($to, $subject, $text);
    }

    /**
     * Send news to users
     *
     * @param string $subject - subject of news
     * @param string $category
     * @param int    $language
     * @param int    $priority
     * @param string $text    - news text, may be in html format
     *
     * @return MTRetCode
     */
    public function NewsSend($subject, $category, $language, $priority, $text)
    {
        $this->connectIfNotConnected();

        $mt_news = new MTNewsProtocol($this->connector);
        return $mt_news->NewsSend($subject, $category, $language, $priority, $text);
    }

    /**
     * Trade balance
     *
     * @param int                 $login user login
     * @param MTEnDealAction      $type | int
     * @param double              $balance
     * @param string              $comment
     * @param int                 $ticket
     * @param bool                $margin_check
     *
     * @return MTRetCode
     */
    public function TradeBalance($login, $type, $balance, $comment, &$ticket = null, $margin_check = true)
    {
        $this->connectIfNotConnected();

        $mt_trade = new MTTradeProtocol($this->connector);
        return $mt_trade->TradeBalance($login, $type, $balance, $comment, $ticket, $margin_check);
    }

    /**
     * Send ping to server
     * @return MTRetCode
     */
    public function Ping()
    {
        $this->connectIfNotConnected();

        $mt_ping = new MTPingProtocol($this->connector);
        return $mt_ping->PingSend();
    }

    /**
     * Send custom command to MT server
     *
     * @param string $command
     * @param array  $params
     * @param string $body
     * @param array  $answer
     * @param string $answer_body
     *
     * @return MTRetCode
     */
    public function CustomSend($command, $params, $body, &$answer, &$answer_body)
    {
        $this->connectIfNotConnected();

        $mt_custom = new MTCustomProtocol($this->connector);
        return $mt_custom->CustomSend($command, $params, $body, $answer, $answer_body);
    }

    /**
     * Restart server wich connect
     * @return MTRetCode
     */
    public function ServerRestart()
    {
        $this->connectIfNotConnected();

        $mt_server = new MTServer($this->connector);
        return $mt_server->Restart();
    }

    /**
     * Create class user
     * @return MTUser
     */
    public function UserCreate()
    {
        $this->connectIfNotConnected();

        return MTUser::CreateDefault();
    }

    /**
     * Create class group
     * @return MTConGroup
     */
    public function GroupCreate()
    {
        $this->connectIfNotConnected();

        return MTConGroup::CreateDefault();
    }

    /**
     * Create class symbol
     * @return MTConSymbol
     */
    public function SymbolCreate()
    {
        $this->connectIfNotConnected();

        return MTConSymbol::CreateDefault();
    }

    /**
     * Set flag write logs
     *
     * @param bool $is_write need write logs
     *
     * @return void
     */
    public function SetLoggerIsWrite($is_write)
    {
        $this->connectIfNotConnected();

        MTLogger::setIsWriteLog($is_write);
    }

    /**
     * Set path to write logs
     *
     * @param string $file_path
     *
     * @return void
     */
    public function SetLoggerFilePath($file_path)
    {
        $this->connectIfNotConnected();

        MTLogger::setFilePath($file_path);
    }

    /**
     * Set prefix for log files
     *
     * @param string $prefix
     *
     * @return void
     */
    public function SetLoggerFilePrefix($prefix)
    {
        $this->connectIfNotConnected();

        MTLogger::setFilePrefix($prefix);
    }

    /**
     * Set or unset flag write MTLoggerType::DEBUG logs
     *
     * @param bool $is_write
     *
     * @return void
     */
    public function SetLoggerWriteDebug($is_write)
    {
        $this->connectIfNotConnected();

        MTLogger::setWriteDebug($is_write);
    }
}
