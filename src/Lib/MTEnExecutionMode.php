<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * order execution modes
 */
class MTEnExecutionMode
{
    const EXECUTION_REQUEST = 0; // Request Execution
    const EXECUTION_INSTANT = 1; // Instant Execution
    const EXECUTION_MARKET = 2; // Market Execution
    const EXECUTION_EXCHANGE = 3; // Exchange Execution
    //--- enumeration borders
    const EXECUTION_FIRST = MTEnExecutionMode::EXECUTION_REQUEST;
    const EXECUTION_LAST = MTEnExecutionMode::EXECUTION_EXCHANGE;
}
