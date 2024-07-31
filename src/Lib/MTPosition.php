<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * Position information
 */
class MTPosition
{
    //--- position ticket
    public $Position;
    //--- position ticket in external system (exchange, ECN, etc)
    public $ExternalID;
    //--- owner client login
    public $Login;
    //--- processed dealer login (0-means auto) (first position deal dealer)
    public $Dealer;
    //--- position symbol
    public $Symbol;
    //--- MTEnPositionAction
    public $Action;
    //--- price digits
    public $Digits;
    //--- currency digits
    public $DigitsCurrency;
    //--- position reason (type is MTEnPositionReason)
    public $Reason;
    //--- symbol contract size
    public $ContractSize;
    //--- position create time
    public $TimeCreate;
    //--- position last update time
    public $TimeUpdate;
    //--- modification flags (type is MTPositionEnTradeModifyFlags)
    public $ModifyFlags;
    //--- position weighted average open price
    public $PriceOpen;
    //--- position current price
    public $PriceCurrent;
    //--- position SL price
    public $PriceSL;
    //--- position TP price
    public $PriceTP;
    //--- position volume
    public $Volume;
    //--- position volume
    public $VolumeExt;
    //--- position floating profit
    public $Profit;
    //--- position accumulated swaps
    public $Storage;
    //--- profit conversion rate (from symbol profit currency to deposit currency)
    public $RateProfit;
    //--- margin conversion rate (from symbol margin currency to deposit currency)
    public $RateMargin;
    //--- expert id (filled by expert advisor)
    public $ExpertID;
    //--- expert position id (filled by expert advisor)
    public $ExpertPositionID;
    //--- comment
    public $Comment;
    //--- order activation state (type is MTEnActivation)
    public $ActivationMode;
    //--- order activation time
    public $ActivationTime;
    //--- order activation price
    public $ActivationPrice;
    //--- order activation flags (type is MTEnPositionTradeActivationFlags)
    public $ActivationFlags;
}
