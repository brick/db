<?php

declare(strict_types=1);

namespace Brick\Db;

use Brick\Db\Driver;

class Connection
{
    /**
     * @var Driver\Connection
     */
    private $driverConnection;

    /**
     * @param Driver\Connection $driverConnection
     */
    public function __construct(Driver\Connection $driverConnection)
    {
        $this->driverConnection = $driverConnection;
    }

    /**
     * @return void
     *
     * @throws DbException
     */
    public function beginTransaction() : void
    {
        try {
            $this->driverConnection->beginTransaction();
        } catch (Driver\DriverException $e) {
            throw new DbException($e->getMessage(), 0, $e); // @todo
        }
    }

    /**
     * @return void
     *
     * @throws DbException
     */
    public function commit() : void
    {
        try {
            $this->driverConnection->commit();
        } catch (Driver\DriverException $e) {
            throw new DbException($e->getMessage(), 0, $e); // @todo
        }
    }

    /**
     * @return void
     *
     * @throws DbException
     */
    public function rollBack() : void
    {
        try {
            $this->driverConnection->rollBack();
        } catch (Driver\DriverException $e) {
            throw new DbException($e->getMessage(), 0, $e); // @todo
        }
    }

    /**
     * @param string $statement
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
            throw new DbException($e->getMessage(), 0, $e); // @todo
        }

        return new PreparedStatement($driverStatement);
    }

    /**
     * @param string $statement
     * @param array  $parameters
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

        try {
            $driverStatement = $this->driverConnection->query($statement);
        } catch (Driver\DriverException $e) {
            throw new DbException($e->getMessage(), 0, $e); // @todo
        }

        return new Statement($driverStatement);
    }

    /**
     * @param string $statement
     *
     * @return int
     *
     * @throws DbException
     */
    public function exec(string $statement) : int
    {
        try {
            return $this->driverConnection->exec($statement);
        } catch (Driver\DriverException $e) {
            throw new DbException($e->getMessage(), 0, $e); // @todo
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
            throw new DbException($e->getMessage(), 0, $e); // @todo
        }
    }
}
