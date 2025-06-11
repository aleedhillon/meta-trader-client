<?php

namespace Aleedhillon\MetaTraderClient;

use Aleedhillon\MetaTraderClient\Lib\MTDeal;
use Aleedhillon\MetaTraderClient\Lib\MTUser;
use Aleedhillon\MetaTraderClient\Lib\MTOrder;
use Aleedhillon\MetaTraderClient\Lib\MTServer;
use Aleedhillon\MetaTraderClient\Lib\MTAccount;
use Aleedhillon\MetaTraderClient\Lib\MTConnect;
use Aleedhillon\MetaTraderClient\Lib\MTConTime;
use Aleedhillon\MetaTraderClient\Lib\MTRetCode;
use Aleedhillon\MetaTraderClient\Lib\MTConGroup;
use Aleedhillon\MetaTraderClient\Lib\MTPosition;
use Aleedhillon\MetaTraderClient\Lib\MTConCommon;
use Aleedhillon\MetaTraderClient\Lib\MTConSymbol;
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
use Aleedhillon\MetaTraderClient\Exceptions\MetaTraderException;
use Aleedhillon\MetaTraderClient\Lib\MTUtils;
use Aleedhillon\MetaTraderClient\Lib\MTEnTradeMode;

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
        $shouldCrypt = true,
        $ip = null,
        $port = null,
        $timeout = null,
        $login = null,
        $password = null
    ) {
        $this->agent = $agent;
        $this->shouldCrypt = $shouldCrypt;

        $this->ip = $ip;
        $this->port = $port;
        $this->timeout = $timeout;
        $this->login = $login;
        $this->password = $password;
    }

    public function connect(): void
    {
        //--- create connection class
        $this->connector = new MTConnect($this->ip, $this->port, $this->timeout, $this->shouldCrypt);
        //--- create connection
        $connectionResponseCode = $this->connector->Connect();

        if ($connectionResponseCode != MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($connectionResponseCode);
        }
        //--- authorization to MetaTrader 5 server
        $authenticator = new MTAuthProtocol($this->connector, $this->agent);
        //---
        $cryptRand = '';

        $authRespondeCode = $authenticator->Auth($this->login, $this->password, $this->shouldCrypt, $cryptRand);
        if ($authRespondeCode != MTRetCode::MT_RET_OK) {
            //--- disconnect
            $this->disconnect();
            throw MetaTraderException::fromMtCode($authRespondeCode);
        }
        //--- if need crypt
        if ($this->shouldCrypt)
            $this->connector->SetCryptRand($cryptRand, $this->password);
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

    public function connectIfNotConnected(): void
    {
        if (!$this->isConnected()) {
            $this->connect();
        }
    }

    /**
     * Get current time from server
     *
     * @return MTConTime
     * @throws MetaTraderException
     */
    public function timeGet(): MTConTime
    {
        $this->connectIfNotConnected();

        $time = null;
        $mtTime = new MTTimeProtocol($this->connector);
        $result = $mtTime->TimeGet($time);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        if ($time === null) {
            throw MetaTraderException::fromMtCode(MTRetCode::MT_RET_ERR_DATA);
        }

        return $time;
    }

    /**
     * Get current time from server
     * @return int - time in unix format
     * @throws MetaTraderException
     */
    public function timeServer(): int
    {
        $this->connectIfNotConnected();

        $mtTime = new MTTimeProtocol($this->connector);
        $result = $mtTime->TimeServer();

        // TimeServer returns the actual time or error code, need to check if it's an error
        if ($result < 0) {
            throw MetaTraderException::fromMtCode($result);
        }

        return $result;
    }

    /**
     * Get common information
     *
     * @return MTConCommon
     * @throws MetaTraderException
     */
    public function commonGet(): MTConCommon
    {
        $this->connectIfNotConnected();

        $common = null;
        $mtCommon = new MTCommonProtocol($this->connector);
        $result = $mtCommon->CommonGet($common);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        if ($common === null) {
            throw MetaTraderException::fromMtCode(MTRetCode::MT_RET_ERR_DATA);
        }

        return $common;
    }

    /**
     * Get count of groups
     *
     * @return int - count groups
     * @throws MetaTraderException
     */
    public function groupTotal(): int
    {
        $this->connectIfNotConnected();

        $total = null;
        $mtGroup = new MTGroupProtocol($this->connector);
        $result = $mtGroup->GroupTotal($total);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        return (int) $total;
    }

    /**
     * Get next group
     *
     * @param int $pos - position
     * @return MTConGroup - next group
     * @throws MetaTraderException
     */
    public function groupNext(int $pos): MTConGroup
    {
        $this->connectIfNotConnected();

        $groupNext = null;
        $mtGroup = new MTGroupProtocol($this->connector);
        $result = $mtGroup->GroupNext($pos, $groupNext);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        if ($groupNext === null) {
            throw MetaTraderException::fromMtCode(MTRetCode::MT_RET_ERR_DATA);
        }

        return $groupNext;
    }

    /**
     * Get group by name
     *
     * @param string $name - name group
     * @return MTConGroup
     * @throws MetaTraderException
     */
    public function groupGet(string $name): MTConGroup
    {
        $this->connectIfNotConnected();

        $group = null;
        $mtGroup = new MTGroupProtocol($this->connector);
        $result = $mtGroup->GroupGet($name, $group);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        if ($group === null) {
            throw MetaTraderException::fromMtCode(MTRetCode::MT_RET_ERR_DATA);
        }

        return $group;
    }

    /**
     * Add or update group
     *
     * @param MTConGroup $group
     * @return MTConGroup
     * @throws MetaTraderException
     */
    public function groupAdd(MTConGroup $group): MTConGroup
    {
        $this->connectIfNotConnected();

        $newGroup = null;
        $mtGroup = new MTGroupProtocol($this->connector);
        $result = $mtGroup->GroupAdd($group, $newGroup);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        if ($newGroup === null) {
            throw MetaTraderException::fromMtCode(MTRetCode::MT_RET_ERR_DATA);
        }

        return $newGroup;
    }

    /**
     * Delete group by name
     *
     * @param string $name - name group
     * @return void
     * @throws MetaTraderException
     */
    public function groupDelete(string $name): void
    {
        $this->connectIfNotConnected();

        $mtGroup = new MTGroupProtocol($this->connector);
        $result = $mtGroup->GroupDelete($name);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }
    }

    /**
     * Get count symbols
     *
     * @return int - get total symbols
     * @throws MetaTraderException
     */
    public function symbolTotal(): int
    {
        $this->connectIfNotConnected();

        $total = null;
        $symbol = new MTSymbolProtocol($this->connector);
        $result = $symbol->SymbolTotal($total);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        return (int) $total;
    }

    /**
     * Get next symbol
     *
     * @param int $pos
     * @return MTConSymbol
     * @throws MetaTraderException
     */
    public function symbolNext(int $pos): MTConSymbol
    {
        $this->connectIfNotConnected();

        $symbolNext = null;
        $mtSymbol = new MTSymbolProtocol($this->connector);
        $result = $mtSymbol->SymbolNext($pos, $symbolNext);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        if ($symbolNext === null) {
            throw MetaTraderException::fromMtCode(MTRetCode::MT_RET_ERR_DATA);
        }

        return $symbolNext;
    }

    /**
     * Get symbol
     *
     * @param string $name
     * @return MTConSymbol
     * @throws MetaTraderException
     */
    public function symbolGet(string $name): MTConSymbol
    {
        $this->connectIfNotConnected();

        $symbol = null;
        $mtSymbol = new MTSymbolProtocol($this->connector);
        $result = $mtSymbol->SymbolGet($name, $symbol);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        if ($symbol === null) {
            throw MetaTraderException::fromMtCode(MTRetCode::MT_RET_ERR_DATA);
        }

        return $symbol;
    }

    /**
     * Get config symbol
     *
     * @param string $name - symbol name
     * @param string $group - group name
     * @return MTConSymbol
     * @throws MetaTraderException
     */
    public function symbolGetGroup(string $name, string $group): MTConSymbol
    {
        $this->connectIfNotConnected();

        $symbol = null;
        $mtSymbol = new MTSymbolProtocol($this->connector);
        $result = $mtSymbol->SymbolGetGroup($name, $group, $symbol);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        if ($symbol === null) {
            throw MetaTraderException::fromMtCode(MTRetCode::MT_RET_ERR_DATA);
        }

        return $symbol;
    }

    /**
     * Symbol add and update
     *
     * @param MTConSymbol $symbol - symbol need add
     * @return MTConSymbol - symbol added to server
     * @throws MetaTraderException
     */
    public function symbolAdd(MTConSymbol $symbol): MTConSymbol
    {
        $this->connectIfNotConnected();

        $newSymbol = null;
        $mtSymbol = new MTSymbolProtocol($this->connector);
        $result = $mtSymbol->SymbolAdd($symbol, $newSymbol);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        if ($newSymbol === null) {
            throw MetaTraderException::fromMtCode(MTRetCode::MT_RET_ERR_DATA);
        }

        return $newSymbol;
    }

    /**
     * Symbol delete
     *
     * @param string $name
     * @return void
     * @throws MetaTraderException
     */
    public function symbolDelete(string $name): void
    {
        $this->connectIfNotConnected();

        $mtSymbol = new MTSymbolProtocol($this->connector);
        $result = $mtSymbol->SymbolDelete($name);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }
    }

    /**
     * Add user to server
     *
     * @param MTUser $user - user add to server
     * @return MTUser - user added to server
     * @throws MetaTraderException
     */
    public function userAdd(MTUser $user): MTUser
    {
        $this->connectIfNotConnected();

        $newUser = null;
        $mtUser = new MTUserProtocol($this->connector);
        $result = $mtUser->Add($user, $newUser);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        if ($newUser === null) {
            throw MetaTraderException::fromMtCode(MTRetCode::MT_RET_ERR_DATA);
        }

        return $newUser;
    }

    /**
     * Update user to server
     *
     * @param MTUser $user - user to update
     * @return MTUser - updated user
     * @throws MetaTraderException
     */
    public function userUpdate(MTUser $user): MTUser
    {
        $this->connectIfNotConnected();

        $newUser = null;
        $mtUser = new MTUserProtocol($this->connector);
        $result = $mtUser->Update($user, $newUser);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        if ($newUser === null) {
            throw MetaTraderException::fromMtCode(MTRetCode::MT_RET_ERR_DATA);
        }

        return $newUser;
    }

    /**
     * User delete from server
     *
     * @param int $login
     * @return void
     * @throws MetaTraderException
     */
    public function userDelete(int $login): void
    {
        $this->connectIfNotConnected();

        $mtUser = new MTUserProtocol($this->connector);
        $result = $mtUser->Delete($login);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }
    }

    /**
     * Get user
     *
     * @param int $login
     * @return MTUser
     * @throws MetaTraderException
     */
    public function userGet(int $login): MTUser
    {
        $this->connectIfNotConnected();

        $user = null;
        $mtUser = new MTUserProtocol($this->connector);
        $result = $mtUser->Get($login, $user);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        if ($user === null) {
            throw MetaTraderException::fromMtCode(MTRetCode::MT_RET_ERR_DATA);
        }

        return $user;
    }

    /**
     * Check login and password
     *
     * @param int $login
     * @param string $password
     * @param string $type
     * @return bool
     * @throws MetaTraderException
     */
    public function userPasswordCheck(int $login, string $password, string $type = MTProtocolConsts::WEB_VAL_USER_PASS_MAIN): bool
    {
        $this->connectIfNotConnected();

        $mtUser = new MTUserProtocol($this->connector);
        $result = $mtUser->PasswordCheck($login, $password, $type);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        return true;
    }

    /**
     * User change password
     *
     * @param int $login
     * @param string $newPassword - new password
     * @param string $type
     * @return void
     * @throws MetaTraderException
     */
    public function userPasswordChange(int $login, string $newPassword, string $type = MTProtocolConsts::WEB_VAL_USER_PASS_MAIN): void
    {
        $this->connectIfNotConnected();

        $mtUser = new MTUserProtocol($this->connector);
        $result = $mtUser->PasswordChange($login, $newPassword, $type);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }
    }

    /**
     * User deposit change
     *
     * @param int $login
     * @param float $newDeposit - new deposit
     * @param string $comment - comment
     * @param MTEnDealAction $type
     * @return void
     * @throws MetaTraderException
     */
    public function userDepositChange(int $login, float $newDeposit, string $comment, MTEnDealAction $type): void
    {
        $this->connectIfNotConnected();

        $mtUser = new MTUserProtocol($this->connector);
        $result = $mtUser->DepositChange($login, $newDeposit, $comment, $type);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }
    }

    /**
     * Get account information
     *
     * @param int $login
     * @return MTAccount
     * @throws MetaTraderException
     */
    public function userAccountGet(int $login): MTAccount
    {
        $this->connectIfNotConnected();

        $account = null;
        $mtUser = new MTUserProtocol($this->connector);
        $result = $mtUser->AccountGet($login, $account);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        if ($account === null) {
            throw MetaTraderException::fromMtCode(MTRetCode::MT_RET_ERR_DATA);
        }

        return $account;
    }

    /**
     * Get list users login
     *
     * @param string $group
     * @return array
     * @throws MetaTraderException
     */
    public function userLogins(string $group): array
    {
        $this->connectIfNotConnected();

        $logins = null;
        $mtUser = new MTUserProtocol($this->connector);
        $result = $mtUser->UserLogins($group, $logins);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        return $logins ?? [];
    }

    /**
     * Get order
     *
     * @param int $ticket
     * @return MTOrder
     * @throws MetaTraderException
     */
    public function orderGet(int $ticket): MTOrder
    {
        $this->connectIfNotConnected();

        $order = null;
        $mtOrder = new MTOrderProtocol($this->connector);
        $result = $mtOrder->OrderGet($ticket, $order);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        if ($order === null) {
            throw MetaTraderException::fromMtCode(MTRetCode::MT_RET_ERR_DATA);
        }

        return $order;
    }

    /**
     * Get all user orders
     *
     * @param int $login - user login
     * @return int - count of orders
     * @throws MetaTraderException
     */
    public function orderGetTotal(int $login): int
    {
        $this->connectIfNotConnected();

        $total = null;
        $mtOrder = new MTOrderProtocol($this->connector);
        $result = $mtOrder->OrderGetTotal($login, $total);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        return (int) $total;
    }

    /**
     * Get orders by page
     *
     * @param int $login - user login
     * @param int $offset - record begin
     * @param int $total - count needs orders
     * @return array
     * @throws MetaTraderException
     */
    public function orderGetPage(int $login, int $offset, int $total): array
    {
        $this->connectIfNotConnected();

        $orders = null;
        $mtOrder = new MTOrderProtocol($this->connector);
        $result = $mtOrder->OrderGetPage($login, $offset, $total, $orders);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        return $orders ?? [];
    }

    /**
     * Get position
     *
     * @param int $login
     * @param string $symbol
     * @return MTPosition
     * @throws MetaTraderException
     */
    public function positionGet(int $login, string $symbol): MTPosition
    {
        $this->connectIfNotConnected();

        $position = null;
        $mtPosition = new MTPositionProtocol($this->connector);
        $result = $mtPosition->PositionGet($login, $symbol, $position);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        if ($position === null) {
            throw MetaTraderException::fromMtCode(MTRetCode::MT_RET_ERR_DATA);
        }

        return $position;
    }

    /**
     * Get all user positions
     *
     * @param int $login - user login
     * @return int - count of positions
     * @throws MetaTraderException
     */
    public function positionGetTotal(int $login): int
    {
        $this->connectIfNotConnected();

        $total = null;
        $mtPosition = new MTPositionProtocol($this->connector);
        $result = $mtPosition->PositionGetTotal($login, $total);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        return (int) $total;
    }

    /**
     * Get positions by page
     *
     * @param int $login - user login
     * @param int $offset - record begin
     * @param int $total - count needs orders
     * @return array
     * @throws MetaTraderException
     */
    public function positionGetPage(int $login, int $offset, int $total): array
    {
        $this->connectIfNotConnected();

        $positions = null;
        $mtPosition = new MTPositionProtocol($this->connector);
        $result = $mtPosition->PositionGetPage($login, $offset, $total, $positions);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        return $positions ?? [];
    }

    /**
     * Get deal
     *
     * @param int $ticket
     * @return MTDeal
     * @throws MetaTraderException
     */
    public function dealGet(int $ticket): MTDeal
    {
        $this->connectIfNotConnected();

        $deal = null;
        $mtDeal = new MTDealProtocol($this->connector);
        $result = $mtDeal->DealGet($ticket, $deal);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        if ($deal === null) {
            throw MetaTraderException::fromMtCode(MTRetCode::MT_RET_ERR_DATA);
        }

        return $deal;
    }

    /**
     * Get count deals
     *
     * @param int $login - user login
     * @param int $from - from date
     * @param int $to - to date
     * @return int - count of deals
     * @throws MetaTraderException
     */
    public function dealGetTotal(int $login, int $from, int $to): int
    {
        $this->connectIfNotConnected();

        $total = null;
        $mtDeal = new MTDealProtocol($this->connector);
        $result = $mtDeal->DealGetTotal($login, $from, $to, $total);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        return (int) $total;
    }

    /**
     * Get deals by page
     *
     * @param int $login - user login
     * @param int $from - from date
     * @param int $to - to date
     * @param int $offset - record begin
     * @param int $total - count needs deals
     * @return array
     * @throws MetaTraderException
     */
    public function dealGetPage(int $login, int $from, int $to, int $offset, int $total): array
    {
        $this->connectIfNotConnected();

        $deals = null;
        $mtDeal = new MTDealProtocol($this->connector);
        $result = $mtDeal->DealGetPage($login, $from, $to, $offset, $total, $deals);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        return $deals ?? [];
    }

    /**
     * Get history
     *
     * @param int $ticket
     * @return MTOrder
     * @throws MetaTraderException
     */
    public function historyGet(int $ticket): MTOrder
    {
        $this->connectIfNotConnected();

        $history = null;
        $mtHistory = new MTHistoryProtocol($this->connector);
        $result = $mtHistory->HistoryGet($ticket, $history);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        if ($history === null) {
            throw MetaTraderException::fromMtCode(MTRetCode::MT_RET_ERR_DATA);
        }

        return $history;
    }

    /**
     * Get count history
     *
     * @param int $login - user login
     * @param int $from - from date
     * @param int $to - to date
     * @return int - count of history
     * @throws MetaTraderException
     */
    public function historyGetTotal(int $login, int $from, int $to): int
    {
        $this->connectIfNotConnected();

        $total = null;
        $mtHistory = new MTHistoryProtocol($this->connector);
        $result = $mtHistory->HistoryGetTotal($login, $from, $to, $total);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        return (int) $total;
    }

    /**
     * Get history by page
     *
     * @param int $login - user login
     * @param int $from - from date
     * @param int $to - to date
     * @param int $offset - record begin
     * @param int $total - count needs orders
     * @return array
     * @throws MetaTraderException
     */
    public function historyGetPage(int $login, int $from, int $to, int $offset, int $total): array
    {
        $this->connectIfNotConnected();

        $orders = null;
        $mtHistory = new MTHistoryProtocol($this->connector);
        $result = $mtHistory->HistoryGetPage($login, $from, $to, $offset, $total, $orders);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        return $orders ?? [];
    }

    /**
     * Get last ticks
     *
     * @param string $symbol
     * @return array
     * @throws MetaTraderException
     */
    public function tickLast(string $symbol): array
    {
        $this->connectIfNotConnected();

        $ticks = null;
        $mtTick = new MTTickProtocol($this->connector);
        $result = $mtTick->TickLast($symbol, $ticks);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        return $ticks ?? [];
    }

    /**
     * Get last ticks by symbol and group
     *
     * @param string $symbol
     * @param string $group
     * @return array
     * @throws MetaTraderException
     */
    public function tickLastGroup(string $symbol, string $group): array
    {
        $this->connectIfNotConnected();

        $ticks = null;
        $mtTick = new MTTickProtocol($this->connector);
        $result = $mtTick->TickLastGroup($symbol, $group, $ticks);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        return $ticks ?? [];
    }

    /**
     * Get tick statistics
     *
     * @param string $symbol
     * @return array
     * @throws MetaTraderException
     */
    public function tickStat(string $symbol): array
    {
        $this->connectIfNotConnected();

        $tickStat = null;
        $mtTick = new MTTickProtocol($this->connector);
        $result = $mtTick->TickStat($symbol, $tickStat);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        return $tickStat ?? [];
    }

    /**
     * Send mail to user
     *
     * @param string $to - user login or mask
     * @param string $subject - subject of mail
     * @param string $text - mail text, may be in html format
     * @return void
     * @throws MetaTraderException
     */
    public function mailSend(string $to, string $subject, string $text): void
    {
        $this->connectIfNotConnected();

        $mtMail = new MTMailProtocol($this->connector);
        $result = $mtMail->MailSend($to, $subject, $text);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }
    }

    /**
     * Send news to users
     *
     * @param string $subject - subject of news
     * @param string $category
     * @param int $language
     * @param int $priority
     * @param string $text - news text, may be in html format
     * @return void
     * @throws MetaTraderException
     */
    public function newsSend(string $subject, string $category, int $language, int $priority, string $text): void
    {
        $this->connectIfNotConnected();

        $mtNews = new MTNewsProtocol($this->connector);
        $result = $mtNews->NewsSend($subject, $category, $language, $priority, $text);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }
    }

    /**
     * Trade balance
     *
     * @param int $login user login
     * @param MTEnDealAction $type
     * @param float $balance
     * @param string $comment
     * @param bool $marginCheck
     * @return int|null - ticket if applicable
     * @throws MetaTraderException
     */
    public function tradeBalance(int $login, MTEnDealAction $type, float $balance, string $comment, bool $marginCheck = true): ?int
    {
        $this->connectIfNotConnected();

        $ticket = null;
        $mtTrade = new MTTradeProtocol($this->connector);
        $result = $mtTrade->TradeBalance($login, $type, $balance, $comment, $ticket, $marginCheck);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        return $ticket;
    }

    /**
     * Send ping to server
     * 
     * @return void
     * @throws MetaTraderException
     */
    public function ping(): void
    {
        $this->connectIfNotConnected();

        $mtPing = new MTPingProtocol($this->connector);
        $result = $mtPing->PingSend();

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }
    }

    /**
     * Send custom command to MT server
     *
     * @param string $command
     * @param array $params
     * @param string $body
     * @return array - response with answer and answer_body keys
     * @throws MetaTraderException
     */
    public function customSend(string $command, array $params, string $body): array
    {
        $this->connectIfNotConnected();

        $answer = null;
        $answerBody = null;
        $mtCustom = new MTCustomProtocol($this->connector);
        $result = $mtCustom->CustomSend($command, $params, $body, $answer, $answerBody);

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }

        return [
            'answer' => $answer ?? [],
            'answer_body' => $answerBody ?? ''
        ];
    }

    /**
     * Restart server which connect
     * 
     * @return void
     * @throws MetaTraderException
     */
    public function serverRestart(): void
    {
        $this->connectIfNotConnected();

        $mtServer = new MTServer($this->connector);
        $result = $mtServer->Restart();

        if ($result !== MTRetCode::MT_RET_OK) {
            throw MetaTraderException::fromMtCode($result);
        }
    }

    /**
     * Create default user instance
     * 
     * @return MTUser
     */
    public function userCreate(): MTUser
    {
        return MTUser::CreateDefault();
    }

    /**
     * Create default group instance
     * 
     * @return MTConGroup
     */
    public function groupCreate(): MTConGroup
    {
        return MTConGroup::CreateDefault();
    }

    /**
     * Create default symbol instance
     * 
     * @return MTConSymbol
     */
    public function symbolCreate(): MTConSymbol
    {
        return MTConSymbol::CreateDefault();
    }

    // ================================
    // UTILITY METHODS
    // ================================

    /**
     * Get error description from MT5 error code
     * 
     * @param int $errorCode
     * @return string
     */
    public static function getErrorDescription(int $errorCode): string
    {
        // Create exception to get error message, then return just the message
        return MetaTraderException::fromMtCode($errorCode)->getMessage();
    }

    /**
     * Convert old 4-digit volume format to new 8-digit format
     * 
     * @param int $oldVolume
     * @return int
     */
    public static function toNewVolume(int $oldVolume): int
    {
        return MTUtils::ToNewVolume($oldVolume);
    }

    /**
     * Convert new 8-digit volume format to old 4-digit format
     * 
     * @param int $newVolume
     * @return int
     */
    public static function toOldVolume(int $newVolume): int
    {
        return MTUtils::ToOldVolume($newVolume);
    }

    /**
     * Validate trade mode value
     * 
     * @param int $tradeMode
     * @return int|null Returns validated trade mode or null if invalid
     */
    public static function validateTradeMode(int $tradeMode): ?int
    {
        return MTEnTradeMode::Get($tradeMode);
    }

    /**
     * Get default margin rates array
     * 
     * @return array
     */
    public static function getDefaultMarginRates(): array
    {
        return MTConSymbol::GetDefaultMarginRate();
    }

    /**
     * Escape special characters for MT5 protocol
     * 
     * @param string $str
     * @return string
     */
    public static function escapeProtocolString(string $str): string
    {
        return MTUtils::Quotes($str);
    }

    /**
     * Generate random hex string (useful for testing)
     * 
     * @param int $length
     * @return string
     */
    public static function generateRandomHex(int $length): string
    {
        return MTUtils::GetRandomHex($length);
    }

    /**
     * Convert hex string to binary string
     * 
     * @param string $hexString
     * @return string
     */
    public static function hexToBinary(string $hexString): string
    {
        return MTUtils::GetFromHex($hexString);
    }

    /**
     * Convert binary data to hex string
     * 
     * @param array|string $bytes
     * @return string
     */
    public static function binaryToHex($bytes): string
    {
        return MTUtils::GetHexFromBytes($bytes);
    }

    /**
     * Get version information
     * 
     * @return array
     */
    public static function getVersionInfo(): array
    {
        return [
            'web_api_version' => WebAPIVersion,
            'web_api_date' => WebAPIDate,
            'php_version' => PHP_VERSION,
            'package_version' => '2.0.0' // Update this with actual package version
        ];
    }

    /**
     * Check if a symbol name is valid format
     * 
     * @param string $symbol
     * @return bool
     */
    public static function isValidSymbolName(string $symbol): bool
    {
        // MT5 symbol names are typically 3-12 characters, alphanumeric + some special chars
        return preg_match('/^[A-Za-z0-9._-]{1,32}$/', $symbol) === 1;
    }

    /**
     * Check if a login number is in valid range
     * 
     * @param int $login
     * @return bool
     */
    public static function isValidLogin(int $login): bool
    {
        // MT5 logins are typically positive integers
        return $login > 0 && $login <= PHP_INT_MAX;
    }

    /**
     * Format MT5 timestamp to human readable date
     * 
     * @param int $mtTimestamp
     * @param string $format
     * @return string
     */
    public static function formatMtTimestamp(int $mtTimestamp, string $format = 'Y-m-d H:i:s'): string
    {
        return date($format, $mtTimestamp);
    }

    /**
     * Convert PHP timestamp to MT5 timestamp
     * 
     * @param int|null $phpTimestamp If null, uses current time
     * @return int
     */
    public static function toMtTimestamp(?int $phpTimestamp = null): int
    {
        return $phpTimestamp ?? time();
    }
}
