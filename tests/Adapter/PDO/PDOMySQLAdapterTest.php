<?php

declare(strict_types=1);

namespace Brick\Db\Tests\Adapter\PDO;

/**
 * PDO MySQL adapter tests.
 */
abstract class PDOMySQLAdapterTest extends PDOAdapterTest
{
    /**
     * @inheritdoc
     */
    protected static function getPDO() : \PDO
    {
        return new \PDO('mysql:host=localhost;dbname=test', 'root', '');
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
