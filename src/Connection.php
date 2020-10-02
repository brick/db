<?php

declare(strict_types=1);

namespace Brick\Db;

use Brick\Db\Driver;
use Brick\Db\Internal\TimerLogger;

class Connection
{
    protected Driver\Connection $driverConnection;

    protected Platform $platform;

    protected TransactionManager $transactionManager;

    protected TimerLogger $logger;

    /**
     * @param Driver\Connection $driverConnection The driver connection.
     * @param Logger|null       $logger           An optional logger.
     * @param Platform|null     $platform         The database platform, or null to auto-detect.
     *
     * @throws DbException
     */
    public function __construct(Driver\Connection $driverConnection, Logger|null $logger = null, Platform|null $platform = null)
    {
        if ($platform === null) {
            $platform = $driverConnection->detectPlatform();

            if ($platform === null) {
                throw new DbException('Cannot detect platform, or platform not supported.');
            }
        }

        $this->driverConnection   = $driverConnection;
        $this->platform           = $platform;
        $this->transactionManager = new TransactionManager($this);
        $this->logger             = new TimerLogger($logger);
    }

    /**
     * @todo support for platforms with a different syntax
     *
     * @param int $isolationLevel
     *
     * @return void
     *
     * @throws DbException
     */
    private function setTransactionIsolationLevel(int $isolationLevel) : void
    {
        $this->exec(
            'SET TRANSACTION ISOLATION LEVEL ' .
            $this->transactionIsolationLevelToString($isolationLevel)
        );
    }

    /**
     * @param int $isolationLevel
     *
     * @return string
     *
     * @throws DbException
     */
    private function transactionIsolationLevelToString(int $isolationLevel) : string
    {
        switch ($isolationLevel) {
            case IsolationLevel::READ_UNCOMMITTED:
                return 'READ UNCOMMITTED';

            case IsolationLevel::READ_COMMITTED:
                return 'READ COMMITTED';

            case IsolationLevel::REPEATABLE_READ:
                return 'REPEATABLE READ';

            case IsolationLevel::SERIALIZABLE:
                return 'SERIALIZABLE';

            default:
                throw new DbException('Invalid transaction isolation level.');
        }
    }

    /**
     * @param int $isolationLevel The minimum transaction isolation level, as an IsolationLevel constant.
     *                            The actual isolation level might be higher, depending on the database platform.
     *                            Defaults to READ_COMMITTED.
     *
     * @return Transaction
     *
     * @throws DbException
     */
    public function beginTransaction(int $isolationLevel = IsolationLevel::READ_COMMITTED) : Transaction
    {
        $this->setTransactionIsolationLevel($isolationLevel);

        return $this->transactionManager->begin();
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
            throw $this->platform->convertException($e, $statement);
        }

        return new PreparedStatement($driverStatement, $statement, $this->platform, $this->logger);
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
            throw $this->platform->convertException($e, $statement);
        } finally {
            $this->logger->stop();
        }

        return new Statement($driverStatement, $statement, $this->platform);
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
            throw $this->platform->convertException($e, $statement);
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
    public function lastInsertId(?string $name = null) : int
    {
        try {
            return $this->driverConnection->lastInsertId($name);
        } catch (Driver\DriverException $e) {
            throw $this->platform->convertException($e);
        }
    }
}
