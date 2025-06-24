<?php

namespace Aleedhillon\MetaTraderClient\Exceptions;

use Exception;
use Aleedhillon\MetaTraderClient\Lib\MTRetCode;

class MetaTraderException extends Exception
{
    protected int $mtCode;

    public function __construct(mixed $mtCode, string $message = '', ?Exception $previous = null)
    {
        $this->mtCode = (int) $mtCode;

        if (empty($message)) {
            $message = MTRetCode::GetError($mtCode);
        }

        parent::__construct($message, $this->mtCode, $previous);
    }

    public function getMtCode(): int
    {
        return $this->mtCode;
    }

    public static function fromMtCode(mixed $mtCode): self
    {
        $mtCodeInt = (int) $mtCode;

        // Determine the specific exception type based on error code range
        if ($mtCodeInt >= 1000 && $mtCodeInt <= 1023) {
            return new AuthenticationException($mtCodeInt);
        }

        if ($mtCodeInt >= 2000 && $mtCodeInt <= 2012) {
            return new ConfigurationException($mtCodeInt);
        }

        if ($mtCodeInt >= 3001 && $mtCodeInt <= 3012) {
            return new UserManagementException($mtCodeInt);
        }

        if ($mtCodeInt >= 4001 && $mtCodeInt <= 4005) {
            return new TradeManagementException($mtCodeInt);
        }

        if ($mtCodeInt >= 10001 && $mtCodeInt <= 11002) {
            return new TradingException($mtCodeInt);
        }

        if ($mtCodeInt >= 5001 && $mtCodeInt <= 6001) {
            return new ReportException($mtCodeInt);
        }

        if ($mtCodeInt >= 7 && $mtCodeInt <= 10) {
            return new NetworkException($mtCodeInt);
        }

        // Default to base exception for other errors
        return new self($mtCodeInt);
    }
}
