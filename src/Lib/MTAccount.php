<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * Trade account interface
 */
class MTAccount
{
    /**
     * login
     * @var int
     */
    public $Login;
    /**
     * currency digits
     * @var int
     */
    public $CurrencyDigits;
    /**
     * balance
     * @var double
     */
    public $Balance;
    /**
     * credit
     * @var double
     */
    public $Credit;
    /**
     * margin
     * @var double
     */
    public $Margin;
    /**
     * free margin
     * @var double
     */
    public $MarginFree;
    /**
     * margin level
     * @var double
     */
    public $MarginLevel;
    /**
     * margin leverage
     * @var int
     */
    public $MarginLeverage;
    /**
     * floating profit
     * @var double
     */
    public $Profit;
    /**
     * storage
     * @var double
     */
    public $Storage;
    /**
     * commission
     * @var double
     */
    public $Commission;
    /**
     * cumulative floating
     * @var double
     */
    public $Floating;
    /**
     * equity
     * @var double
     */
    public $Equity;
    /**
     * stop-out activation mode
     * @var MTEnSoActivation
     */
    public $SOActivation;
    /**
     * stop-out activation time
     * @var int
     */
    public $SOTime;
    /**
     * margin level on stop-out
     * @var double
     */
    public $SOLevel;
    /**
     * equity on stop-out
     * @var double
     */
    public $SOEquity;
    /**
     * margin on stop-out
     * @var double
     */
    public $SOMargin;
    /**
     * account assets
     * @var double
     */
    public $Assets;
    /**
     * account liabilities
     * @var double
     */
    public $Liabilities;
    /**
     * blocked daily & monthly commission
     * @var double
     */
    public $BlockedCommission;
    /**
     * blocked fixed profit
     * @var double
     */
    public $BlockedProfit;
    /**
     * account initial margin
     * @var double
     */
    public $MarginInitial;
    /**
     * account maintenance margin
     * @var double
     */
    public $MarginMaintenance;
}
