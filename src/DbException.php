<?php

declare(strict_types=1);

namespace Brick\Db;

use Brick\Db\Driver\DriverException;

class DbException extends \Exception
{
    /**
     * The SQL statement that generated an exception, if any.
     *
     * @var string|null
     */
    private $sqlStatement;

    /**
     * The parameters bound to the statement, if any.
     *
     * @var array|null
     */
    private $parameters;

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
     * @param DriverException $driverException
     * @param string|null     $sqlStatement
     * @param array|null      $parameters
     *
     * @return DbException
     */
    public static function fromDriverException(DriverException $driverException, ?string $sqlStatement = null, ?array $parameters = null) : DbException
    {
        $message = $driverException->getMessage();

        if ($sqlStatement !== null) {
            $message .= ' While executing: ' . $sqlStatement;
        }

        $exception = new self($message, 0, $driverException);

        $exception->sqlStatement = $sqlStatement;
        $exception->parameters   = $parameters;

        $exception->sqlState  = $driverException->getSQLState();
        $exception->errorCode = $driverException->getErrorCode();

        return $exception;
    }

    /**
     * @return string|null
     */
    public function getSQLStatement() : ?string
    {
        return $this->sqlStatement;
    }

    /**
     * @return array|null
     */
    public function getParameters() : ?array
    {
        return $this->parameters;
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
