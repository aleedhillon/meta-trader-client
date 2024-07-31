<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * Data config of group
 */
class MTConGroup
{
    //--- group name
    public $Group;
    //--- group trade server ID
    public $Server;
    //--- MTEnPermissionsFlags
    public $PermissionsFlags;
    //--- MTEnAuthMode
    public $AuthMode;
    //--- minimal password length
    public $AuthPasswordMin;
    //--- OTP authentication mode (type is MTEnAuthOTPMode)
    public $AuthOTPMode;
    //--- company name
    public $Company;
    //--- company web page URL
    public $CompanyPage;
    //--- company email
    public $CompanyEmail;
    //--- company support site URL
    public $CompanySupportPage;
    //--- company support email
    public $CompanySupportEmail;
    //--- company catalog name (for reports and email templates)
    public $CompanyCatalog;
    //--- deposit currency
    public $Currency;
    public $CurrencyDigits;
    //--- MTEnReportsMode
    public $ReportsMode;
    //--- MTEnReportsFlags
    public $ReportsFlags;
    //--- reports SMTP server address:ports
    public $ReportsSMTP;
    //--- reports SMTP server login
    public $ReportsSMTPLogin;
    //--- reports SMTP server password
    public $ReportsSMTPPass;
    //--- MTEnNewsMode
    public $NewsMode;
    //--- news category filter string
    public $NewsCategory;
    //--- allowed news languages (Windows API LANGID used)
    public $NewsLangs;
    //--- MTEnMailMode
    public $MailMode;
    //--- MTEnGroupTradeFlags
    public $TradeFlags;
    //--- deposit transfer mode (type is MTEnTransferMode)
    public $TradeTransferMode;
    //--- interest rate for free deposit money
    public $TradeInterestrate;
    //--- virtual credit
    public $TradeVirtualCredit;
    //--- group risk management mode (type is MTEnMarginMode)
    public $MarginMode;
    //--- MTEnStopOutMode
    public $MarginSOMode;
    //--- MTEnFreeMarginMode
    public $MarginFreeMode;
    //--- Margin Call level value
    public $MarginCall;
    //--- Sto-Out level value
    public $MarginStopOut;
    //--- MTEnMarginFreeProfitMode
    public $MarginFreeProfitMode;
    //--- default demo accounts leverage
    public $DemoLeverage;
    //--- default demo accounts deposit
    public $DemoDeposit;
    //--- MTEnHistoryLimit
    public $LimitHistory;
    //--- max. order limit
    public $LimitOrders;
    //--- max. selected symbols limit
    public $LimitSymbols;
    //--- max. positions limit
    public $LimitPositions;
    //--- commissions
    public $Commissions;
    //--- groups symbols settings
    public $Symbols;

    /**
     * Create MTConGroup with default values
     * @return MTConGroup
     */
    public static function CreateDefault()
    {
        $group = new MTConGroup();
        //---
        $group->PermissionsFlags = MTEnPermissionsFlags::PERMISSION_ENABLE_CONNECTION;
        $group->AuthMode = MTEnAuthMode::AUTH_STANDARD;
        $group->AuthPasswordMin = 7;
        $group->ReportsMode = MTEnReportsMode::REPORTS_DISABLED;
        $group->ReportsFlags = MTEnReportsFlags::REPORTSFLAGS_NONE;
        $group->Currency = "USD";
        $group->CurrencyDigits = 2;
        $group->NewsMode = MTEnNewsMode::NEWS_MODE_FULL;
        $group->MailMode = MTEnMailMode::MAIL_MODE_FULL;
        $group->MarginFreeMode = MTEnFreeMarginMode::FREE_MARGIN_USE_PL;
        $group->MarginCall = 50;
        $group->MarginStopOut = 30;
        $group->MarginSOMode = MTEnStopOutMode::STOPOUT_PERCENT;
        $group->TradeVirtualCredit = 0;
        $group->MarginFreeProfitMode = MTEnMarginFreeProfitMode::FREE_MARGIN_PROFIT_PL;
        $group->DemoLeverage = 0;
        $group->DemoDeposit = 0;
        $group->LimitSymbols = 0;
        $group->LimitOrders = 0;
        $group->LimitHistory = MTEnHistoryLimit::TRADE_HISTORY_ALL;
        $group->TradeInterestrate = 0;
        $group->TradeFlags = MTEnGroupTradeFlags::TRADEFLAGS_ALL;
        //---
        return $group;
    }
}
