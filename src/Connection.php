<?php

declare(strict_types=1);

namespace Brick\Db;

use Brick\Db\Driver;
use Brick\Db\Internal\TimerLogger;

class Connection
{
    /**
     * @var Driver\Connection
     */
    protected $driverConnection;

    /**
     * @var TimerLogger
     */
    protected $logger;

    /**
     * @param Driver\Connection $driverConnection
     * @param Logger|null       $logger
     */
    public function __construct(Driver\Connection $driverConnection, ?Logger $logger = null)
    {
        $this->driverConnection = $driverConnection;
        $this->logger           = new TimerLogger($logger);
    }

    /**
     * @return void
     *
     * @throws DbException
     */
    public function beginTransaction() : void
    {
        $this->logger->start('BEGIN TRANSACTION');

        try {
            $this->driverConnection->beginTransaction();
        } catch (Driver\DriverException $e) {
            throw DbException::fromDriverException($e);
        } finally {
            $this->logger->stop();
        }
    }

    /**
     * @return void
     *
     * @throws DbException
     */
    public function commit() : void
    {
        $this->logger->start('COMMIT');

        try {
            $this->driverConnection->commit();
        } catch (Driver\DriverException $e) {
            throw DbException::fromDriverException($e);
        } finally {
            $this->logger->stop();
        }
    }

    /**
     * @return void
     *
     * @throws DbException
     */
    public function rollBack() : void
    {
        $this->logger->start('ROLLBACK');

        try {
            $this->driverConnection->rollBack();
        } catch (Driver\DriverException $e) {
            throw DbException::fromDriverException($e);
        } finally {
            $this->logger->stop();
        }
    }

    /**
     * @param string $statement The SQL statement.
     *
     * @return PreparedStatement
     *
     * @throws DbException
     */
    public function prepare(string $statement) : PreparedStatement
    {
        try {
            $driverStatement = $this->driverConnection->prepare($statement);
        } catch (Driver\DriverException $e) {
            throw DbException::fromDriverException($e, $statement);
        }

        return new PreparedStatement($driverStatement, $statement, $this->logger);
    }

    /**
     * @param string $statement  The SQL statement.
     * @param array  $parameters The optional bound parameters.
     *
     * @return Statement
     *
     * @throws DbException
     */
    public function query(string $statement, array $parameters = []) : Statement
    {
        if ($parameters) {
            $statement = $this->prepare($statement);
            $statement->execute($parameters);

            return $statement;
        }

        $this->logger->start($statement);

        try {
            $driverStatement = $this->driverConnection->query($statement);
        } catch (Driver\DriverException $e) {
            throw DbException::fromDriverException($e, $statement);
        } finally {
            $this->logger->stop();
        }

        return new Statement($driverStatement, $statement);
    }

    /**
     * @param string $statement The SQL statement.
     *
     * @return int
     *
     * @throws DbException
     */
    public function exec(string $statement) : int
    {
        $this->logger->start($statement);

        try {
            return $this->driverConnection->exec($statement);
        } catch (Driver\DriverException $e) {
            throw DbException::fromDriverException($e, $statement);
        } finally {
            $this->logger->stop();
        }
    }

    /**
     * @param string|null $name
     *
     * @return int
     *
     * @throws DbException
     */
    public function lastInsertId(string $name = null) : int
    {
        try {
            return $this->driverConnection->lastInsertId($name);
        } catch (Driver\DriverException $e) {
            throw DbException::fromDriverException($e);
        }
    }
}
