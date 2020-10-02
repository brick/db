<?php

namespace Brick\Db\Tests;

use Brick\Db\Connection;
use Brick\Db\Driver\PDO\PDOConnection;
use Brick\Db\Logger\DebugLogger;
use Brick\Db\Logger\DebugStatement;
use PHPUnit\Framework\TestCase;

class ConnectionTest extends TestCase
{
    public function testLogging() : void
    {
        $pdo = new \PDO('sqlite::memory:');
        $pdoConnection = new PDOConnection($pdo);
        $logger = new DebugLogger();
        $connection = new Connection($pdoConnection, $logger);

        $connection->exec('CREATE TABLE test(id INT)');
        $connection->query('INSERT INTO test (id) VALUES(?)', [123]);

        $value = $connection->query('SELECT * FROM test WHERE id = ?', [123])->fetch();
        $this->assertEquals([123], $value);

        $value = $connection->query('SELECT * FROM test WHERE id = ?', [456])->fetch();
        $this->assertNull($value);

        $this->assertDebugStatement('CREATE TABLE test(id INT)', [], $logger->getDebugStatement(0));
        $this->assertDebugStatement('INSERT INTO test (id) VALUES(?)', [123], $logger->getDebugStatement(1));
        $this->assertDebugStatement('SELECT * FROM test WHERE id = ?', [123], $logger->getDebugStatement(2));
        $this->assertDebugStatement('SELECT * FROM test WHERE id = ?', [456], $logger->getDebugStatement(3));

        $this->assertDebugStatement('CREATE TABLE test(id INT)', [], $logger->getDebugStatement(-4));
        $this->assertDebugStatement('INSERT INTO test (id) VALUES(?)', [123], $logger->getDebugStatement(-3));
        $this->assertDebugStatement('SELECT * FROM test WHERE id = ?', [123], $logger->getDebugStatement(-2));
        $this->assertDebugStatement('SELECT * FROM test WHERE id = ?', [456], $logger->getDebugStatement(-1));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('No debug statement at index 4');
        $logger->getDebugStatement(4);
    }

    /**
     * @param string         $statement
     * @param array          $parameters
     * @param DebugStatement $debugStatement
     *
     * @return void
     */
    private function assertDebugStatement(string $statement, array $parameters, DebugStatement $debugStatement) : void
    {
        $this->assertSame($statement, $debugStatement->getStatement());
        $this->assertSame($parameters, $debugStatement->getParameters());
        $this->assertIsFloat($debugStatement->getTime());
        $this->assertGreaterThan(0.0, $debugStatement->getTime());
    }
}
