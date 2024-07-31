<?php
namespace Aleedhillon\MetaTraderClient\Lib;

/**
 * LiveUpdate modes
 */
class MTEnUpdateMode
{
    const UPDATE_DISABLE = 0; // disable LiveUpdate
    const UPDATE_ENABLE = 1; // enable LiveUpdate
    const UPDATE_ENABLE_BETA = 2; // enable LiveUpdate, including beta releases
    //--- enumeration borders
    const UPDATE_FIRST = MTEnUpdateMode::UPDATE_DISABLE;
    const UPDATE_LAST = MTEnUpdateMode::UPDATE_ENABLE_BETA;
}
