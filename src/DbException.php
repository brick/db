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
            $message .= ' while executing: ' . $sqlStatement;

            if ($parameters) {
                $message .= ' with parameters [';
                $message .= implode(', ', array_map([self::class, 'getParameterPreview'], $parameters));
                $message .= ']';
            }
        }

        $exception = new static($message, 0, $driverException);

        $exception->sqlStatement = $sqlStatement;
        $exception->parameters   = $parameters;

        $exception->sqlState  = $driverException->getSQLState();
        $exception->errorCode = $driverException->getErrorCode();

        return $exception;
    }

    /**
     * Returns a preview of the given parameter, to be included in the exception message.
     *
     * @param mixed $parameter
     *
     * @return string
     */
    private static function getParameterPreview(mixed $parameter) : string
    {
        if ($parameter === null) {
            return 'null';
        }

        if (is_int($parameter) || is_float($parameter)) {
            return (string) $parameter;
        }

        if (is_bool($parameter)) {
            return $parameter ? 'true' : 'false';
        }

        if (is_string($parameter)) {
            return self::getStringPreview($parameter);
        }

        return gettype($parameter);
    }

    /**
     * @todo handle UTF-8 strings
     *
     * @param string $string
     *
     * @return string
     */
    private static function getStringPreview(string $string) : string
    {
        $maxLength = 20;

        $result = substr($string, 0, $maxLength);
        $result = preg_replace_callback('/[\x00-\x1F\x7F-\xFF]/', function(array $matches) : string {
            return '\x' . str_pad(bin2hex($matches[0]), 2, '0', STR_PAD_LEFT);
        }, $result);

        if (strlen($string) > $maxLength) {
            $result .= '...';
        }

        return "'$result'";
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
