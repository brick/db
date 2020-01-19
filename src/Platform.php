<?php

declare(strict_types=1);

namespace Brick\Db;

use Brick\Db\Driver\DriverException;
use LogicException;

/**
 * Represents a database platform, such as MySQL, PostgreSQL or SQLite.
 */
abstract class Platform
{
    /**
     * Converts a platform-specific exception thrown by the driver to a cross-platform DbException.
     *
     * @param DriverException $e
     * @param string|null     $sqlStatement
     * @param array|null      $parameters
     *
     * @return DbException
     */
    public function convertException(DriverException $e, ?string $sqlStatement = null, ?array $parameters = null) : DbException
    {
        /** @var DbException $exceptionClass CAUTION: THIS IS A CLASS NAME, NOT AN OBJECT, BUT WE PLEASE THE IDE */
        $exceptionClass = $this->getExceptionClass($e);

        if (! is_a($exceptionClass, DbException::class, true)) {
            throw new LogicException('The Platform did not return a known exception class.');
        }

        return $exceptionClass::fromDriverException($e, $sqlStatement, $parameters);
    }

    /**
     * Returns the DbException class or subclass that best matches a platform-specific exception thrown by the driver.
     *
     * The implementation has the responsibility to return the name of the most specific subclass of DbException that
     * matches the platform-specific error code, and only return the root DbException class if the error code does not
     * match the semantics of any of the DbException subclasses.
     *
     * @param DriverException $e
     *
     * @return string The DbException::class or one of its subclasses.
     */
    abstract protected function getExceptionClass(DriverException $e) : string;
}
