<?php

declare(strict_types=1);

namespace Brick\Db\Tests\Driver\PDO;

/**
 * PDO MySQL adapter tests.
 */
abstract class PDOMySQLDriverTest extends PDODriverTest
{
    /**
     * @inheritdoc
     */
    protected function getPDO() : \PDO
    {
        return new \PDO('mysql:host=localhost;dbname=test', 'root', '');
    }

    /**
     * @inheritdoc
     */
    protected function supportsKillConnection() : bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function killConnection() : void
    {
        // Use a separate connection.
        $pdo = $this->getPDO();

        // Kill all other connections.
        $ids = $pdo->query(
            'SELECT ID FROM information_schema.PROCESSLIST ' .
            'WHERE USER = SUBSTRING_INDEX(CURRENT_USER, "@", 1) ' .
            'AND HOST = SUBSTRING_INDEX(CURRENT_USER, "@", -1) ' .
            'AND STATE != "executing"'
        )->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($ids as $id) {
            $pdo->query("KILL CONNECTION $id");
        }
    }

    /**
     * @inheritdoc
     */
    protected static function getAttributeVariations() : array
    {
        return parent::getAttributeVariations() + [
            \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => [
                false,
                true
            ]
        ];
    }
}
