<?php

declare(strict_types=1);

namespace Brick\Db;

class DbException extends \Exception
{
    /**
     * The SQL statement that generated an exception, if any.
     *
     * @var string|null
     */
    private $sqlStatement;

    /**
     * The five characters SQLSTATE error code, or null if not available.
     *
     * @var string|null
     */
    private $sqlState;

    /**
     * The driver-specific error code, or null if not available.
     *
     * @var string|int|null
     */
    private $errorCode;

    /**
     * DbException constructor.
     *
     * @param string          $message      The error message.
     * @param string|null     $sqlStatement The SQL statement that generated an exception, if any.
     * @param string|null     $sqlState     The five characters SQLSTATE error code, or null if not available.
     * @param string|int|null $errorCode    The driver-specific error code, or null if not available.
     * @param \Throwable|null $previous     The previous exception, if any.
     */
    public function __construct(string $message, ?string $sqlStatement = null, ?string $sqlState = null, $errorCode = null, ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);

        $this->sqlStatement = $sqlStatement;
        $this->sqlState     = $sqlState;
        $this->errorCode    = $errorCode;
    }

    /**
     * @return string|null
     */
    public function getSQLStatement() : ?string
    {
        return $this->sqlStatement;
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
