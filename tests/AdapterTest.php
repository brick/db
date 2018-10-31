<?php

declare(strict_types=1);

namespace Brick\Db\Tests;

use Brick\Db\Connection;

use PHPUnit\Framework\TestCase;

/**
 * Base class for DB adapter tests.
 */
abstract class AdapterTest extends TestCase
{
    /**
     * @var Connection
     */
    private static $connection;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        self::$connection = static::getConnection();
    }

    /**
     * @return Connection
     */
    abstract protected static function getConnection() : Connection;

    /**
     * @expectedException \Brick\Db\DbException
     */
    public function testInvalidQueryThrowsException()
    {
        self::$connection->query('SELECT 1 FROM unknown_table');
    }
}
