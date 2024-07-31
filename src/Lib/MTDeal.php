<?php
namespace Aleedhillon\MetaTraderClient\Lib;


/**
 * Deal information
 */
class MTDeal
{
    /**
     * deal ticket
     * @var int
     */
    public $Deal;
    /**
     * deal ticket in external system (exchange, ECN, etc)
     * @var string
     */
    public $ExternalID;
    /**
     * client login
     * @var int
     */
    public $Login;
    /**
     * processed dealer login (0-means auto)
     * @var int
     */
    public $Dealer;
    /**
     * deal order ticket
     * @var int
     */
    public $Order;
    /**
     * EnDealAction
     * @var EnDealAction
     */
    public $Action;
    /**
     * EnEntryFlags
     * @var EnEntryFlags
     */
    public $Entry;
    /**
     * EnDealReason
     * @var EnDealReason
     */
    public $Reason;
    /**
     * price digits
     * @var int
     */
    public $Digits;
    /**
     * currency digits
     * @var int
     */
    public $DigitsCurrency;
    /**
     * symbol contract size
     * @var double
     */
    public $ContractSize;
    /**
     * deal creation datetime
     * @var int
     */
    public $Time;
    /**
     * deal creation datetime in msc since 1970.01.01
     * @var string
     */
    public $TimeMsc;
    /**
     * deal symbol
     * @var string
     */
    public $Symbol;
    /**
     * deal price
     * @var double
     */
    public $Price;
    /**
     * deal volume
     * @var int
     */
    public $Volume;
    /**
     * deal volume
     * @var int
     */
    public $VolumeExt;
    /**
     * deal profit
     * @var double
     */
    public $Profit;
    /**
     * deal collected swaps
     * @var double
     */
    public $Storage;
    /**
     * deal commission
     * @var double
     */
    public $Commission;
    /**
     * profit conversion rate (from symbol profit currency to deposit currency)
     * @var double
     */
    public $RateProfit;
    /**
     * margin conversion rate (from symbol margin currency to deposit currency)
     * @var double
     */
    public $RateMargin;
    /**
     * expert id (filled by expert advisor)
     * @var int
     */
    public $ExpertID;
    /**
     * position id
     * @var int
     */
    public $PositionID;
    /**
     * deal comment
     * @var string
     */
    public $Comment;
    /**
     * deal profit in symbol's profit currency
     * @var double
     */
    public $ProfitRaw;
    /**
     * closed position  price
     * @var double
     */
    public $PricePosition;
    /**
     * closed volume
     * @var int
     */
    public $VolumeClosed;
    /**
     * closed volume
     * @var int
     */
    public $VolumeClosedExt;
    /**
     * tick value
     * @var double
     */
    public $TickValue;
    /**
     * tick size
     * @var double
     */
    public $TickSize;
    /**
     * flags
     * @var int
     */
    public $Flags;
    /**
     * source gateway name
     * @var string
     */
    public $Gateway;
    /**
     * tick size
     * @var double
     */
    public $PriceGateway;
    /**
     * EnEntryFlags
     * @var EnEntryFlags
     */
    public $ModifyFlags;
}
