<?php

declare(strict_types=1);

namespace Brick\Db\Tests;

use Brick\Db\Driver\Connection;
use Brick\Db\Driver\DriverException;

use PHPUnit\Framework\TestCase;

/**
 * Base class for DB driver tests.
 */
abstract class DriverTest extends TestCase
{
    private Connection $connection;

    /**
     * @inheritdoc
     */
    protected function setUp() : void
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

//    public function testBeginTransactionFailure()
//    {
//        if (! $this->supportsKillConnection()) {
//            self::markTestSkipped();
//        }
//
//        $this->killConnection();
//        $this->expectException(DriverException::class);
//        $this->connection->beginTransaction();
//    }
//
//    public function testCommitFailure()
//    {
//        if (! $this->supportsKillConnection()) {
//            self::markTestSkipped();
//        }
//
//        $this->connection->beginTransaction();
//        $this->killConnection();
//        $this->expectException(DriverException::class);
//        $this->connection->commit();
//    }

    public function testInvalidQueryThrowsException() : void
    {
        $this->expectException(DriverException::class);
        $this->connection->query('SELECT 1 FROM unknown_table');
    }
}
