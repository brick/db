<?php

declare(strict_types=1);

namespace Brick\Db\Tests\Adapter\PDO;

/**
 * PDO SQLite adapter tests.
 */
abstract class PDOSQLiteAdapterTest extends PDOAdapterTest
{
    /**
     * @inheritdoc
     */
    protected static function getPDO() : \PDO
    {
        return new \PDO('sqlite::memory:');
    }
}
