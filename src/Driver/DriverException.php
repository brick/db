<?php

declare(strict_types=1);

namespace Brick\Db\Driver;

use Throwable;

class DriverException extends \Exception
{
    /**
     * The five characters SQLSTATE error code, or null if not available.
     */
    private string|null $sqlState;

    /**
     * The driver-specific error code, or null if not available.
     */
    private string|int|null $errorCode;

    /**
     * DbException constructor.
     *
     * @param string          $message   The error message.
     * @param string|null     $sqlState  The five characters SQLSTATE error code, or null if not available.
     * @param string|int|null $errorCode The driver-specific error code, or null if not available.
     * @param Throwable|null  $previous  The previous exception, if any.
     */
    public function __construct(string $message, string|null $sqlState = null, string|int|null $errorCode = null, Throwable|null $previous = null)
    {
        parent::__construct($message, 0, $previous);

        $this->sqlState  = $sqlState;
        $this->errorCode = $errorCode;
    }

    /**
     * Returns the five characters SQLSTATE error code, or null if not available.
     *
     * @return string|null
     */
    public function getSQLState() : string|null
    {
        return $this->sqlState;
    }

    /**
     * Returns the driver-specific error code, or null if not available.
     */
    public function getErrorCode() : string|int|null
    {
        return $this->errorCode;
    }
}
