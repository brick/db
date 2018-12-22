<?php

declare(strict_types=1);

namespace Brick\Db\Tests\Driver\PDO;

/**
 * PDO SQLite adapter tests.
 */
abstract class PDOSQLiteDriverTest extends PDODriverTest
{
    /**
     * @inheritdoc
     */
    protected function getPDO() : \PDO
    {
        return new \PDO('sqlite::memory:');
    }

    /**
     * @inheritdoc
     */
    protected function supportsKillConnection() : bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    protected function killConnection() : void
    {
        throw new \RuntimeException('Killing connection is not supported on SQLite.');
    }
}
