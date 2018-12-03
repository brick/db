<?php

declare(strict_types=1);

namespace Brick\Db\Tests\Adapter\PDO;

use Brick\Db\Adapter\PDO\PDOConnection;
use Brick\Db\Connection;
use Brick\Db\Tests\AdapterTest;

/**
 * PDO adapter tests.
 */
abstract class PDOAdapterTest extends AdapterTest
{
    /**
     * @return Connection
     */
    protected function getConnection() : Connection
    {
        $pdo = $this->getPDO();

        foreach (static::getAttributes() as $attribute => $value) {
            $pdo->setAttribute($attribute, $value);
        }

        return new PDOConnection($pdo);
    }

    /**
     * Returns a PDO connection to the database.
     *
     * @return \PDO
     */
    abstract protected function getPDO() : \PDO;

    /**
     * Returns an array mapping PDO attributes to their value.
     *
     * @return array
     */
    abstract protected static function getAttributes() : array;

    /**
     * Returns all combinations of PDO attributes.
     *
     * The result is an array of arrays mapping PDO attributes to values.
     *
     * For example:
     *
     * [
     *   [
     *     PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
     *     PDO::ATTR_EMULATE_PREPARES => false
     *   ], [
     *     PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
     *     PDO::ATTR_EMULATE_PREPARES => true
     *   ], [
     *     PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
     *     PDO::ATTR_EMULATE_PREPARES => false
     *   ],
     *   ...
     * ]
     *
     * @return array
     */
    public static function getAttributeCombinations() : array
    {
        $variations = static::getAttributeVariations();

        $result = [[]];

        foreach ($variations as $name => $values) {
            $tmp = [];

            foreach ($result as $item) {
                foreach ($values as $value) {
                    $x = $item;
                    $x[$name] = $value;
                    $tmp[] = $x;
                }
            }

            $result = $tmp;
        }

        return $result;
    }

    /**
     * Returns an associative array of PDO attribute variations to test.
     *
     * This method can be overridden by platform-specific tests.
     *
     * @return array
     */
    protected static function getAttributeVariations() : array
    {
        return [
            \PDO::ATTR_ERRMODE => [
                \PDO::ERRMODE_SILENT,
                \PDO::ERRMODE_WARNING,
                \PDO::ERRMODE_EXCEPTION
            ],
            \PDO::ATTR_EMULATE_PREPARES => [
                false,
                true
            ]
        ];
    }
}
