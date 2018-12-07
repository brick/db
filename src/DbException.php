<?php

declare(strict_types=1);

namespace Brick\Db;

class DbException extends \Exception
{
    /**
     * @var string|null
     */
    private $sqlState;

    /**
     * @var string|int|null
     */
    private $errorCode;

    /**
     * DbException constructor.
     *
     * @param string          $message   The error message.
     * @param string|null     $sqlState  The five characters SQLSTATE error code, or null if not available.
     * @param string|int|null $errorCode The driver-specific error code, or null if not available.
     *
     * @param \Throwable|null $previous
     */
    public function __construct(string $message, ?string $sqlState = null, $errorCode = null, ?\Throwable $previous = null)
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
    public function getSQLState() : ?string
    {
        return $this->sqlState;
    }

    /**
     * Returns the driver-specific error code, or null if not available.
     *
     * @return string|int|null
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }
}