<?php

declare(strict_types=1);

namespace Brick\Db\Tests;

use Brick\Db\Connection;
use Brick\Db\DbException;

use PHPUnit\Framework\TestCase;

/**
 * Base class for DB adapter tests.
 */
abstract class AdapterTest extends TestCase
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->connection = $this->getConnection();
    }

    /**
     * @return Connection
     */
    abstract protected function getConnection() : Connection;

    /**
     * Returns whether killing the current connection is supported.
     *
     * @return bool
     */
    abstract protected function supportsKillConnection() : bool;

    /**
     * Kills the current connection to purposefully trigger an error on the next statement.
     *
     * Note that this method may have the side effect of killing all active connections.
     *
     * @return void
     *
     * @throws \RuntimeException If not supported.
     */
    abstract protected function killConnection() : void;

    public function testBeginTransactionFailure()
    {
        if (! $this->supportsKillConnection()) {
            self::markTestSkipped();
        }

        $this->killConnection();
        $this->expectException(DbException::class);
        $this->connection->beginTransaction();
    }

    public function testCommitFailure()
    {
        if (! $this->supportsKillConnection()) {
            self::markTestSkipped();
        }

        $this->connection->beginTransaction();
        $this->killConnection();
        $this->expectException(DbException::class);
        $this->connection->commit();
    }

    public function testRollBackFailure()
    {
        if (! $this->supportsKillConnection()) {
            self::markTestSkipped();
        }

        $this->connection->beginTransaction();
        $this->killConnection();
        $this->expectException(DbException::class);

        // On MySQL (8.0.13), the first rollBack() fails silently and does not throw an exception. Why?
        $this->connection->rollBack();
        $this->connection->rollBack();
    }

    /**
     * @expectedException \Brick\Db\DbException
     */
    public function testInvalidQueryThrowsException()
    {
        $this->connection->query('SELECT 1 FROM unknown_table');
    }
}
