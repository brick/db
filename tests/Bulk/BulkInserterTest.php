<?php

namespace Brick\Db\Tests\Bulk;

use Brick\Db\Bulk\BulkInserter;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for class BulkInserter.
 */
class BulkInserterTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The number of operations per query must be 1 or more.
     */
    public function testValidateConstructorBatchSize()
    {
        $pdo = new PDOMock();
        new BulkInserter($pdo, 'table', ['id'], 0);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The field list is empty.
     */
    public function testValidateConstructorFieldCount()
    {
        $pdo = new PDOMock();
        new BulkInserter($pdo, 'table', []);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testValidateQueueFieldCount()
    {
        $pdo = new PDOMock();
        $inserter = new BulkInserter($pdo, 'table', ['id', 'name']);
        $inserter->queue(1);
    }

    public function testBulkInsert()
    {
        $pdo = new PDOMock([3, 3, 1]);

        $inserter = new BulkInserter($pdo, 'transactions', ['user', 'currency', 'amount'], 3);

        $inserter->queue(1, 'EUR', '1.23');
        $inserter->queue(2, 'USD', '2.34');
        $inserter->queue(3, 'GBP', '3.45');
        $inserter->queue(4, 'CAD', '4.56');
        $inserter->queue(5, 'USD', '5.67');
        $inserter->queue(6, 'USD', '6.78');
        $inserter->queue(7, 'USD', '7.89');

        $this->assertSame(6, $inserter->getRowCount());

        $inserter->flush();

        $this->assertSame(7, $inserter->getRowCount());

        $expectedLog = [
            "PREPARE STATEMENT 1: INSERT INTO transactions (user, currency, amount) VALUES (?, ?, ?), (?, ?, ?), (?, ?, ?)",
            "EXECUTE STATEMENT 1: (1, 'EUR', '1.23', 2, 'USD', '2.34', 3, 'GBP', '3.45')",
            "EXECUTE STATEMENT 1: (4, 'CAD', '4.56', 5, 'USD', '5.67', 6, 'USD', '6.78')",
            "PREPARE STATEMENT 2: INSERT INTO transactions (user, currency, amount) VALUES (?, ?, ?)",
            "EXECUTE STATEMENT 2: (7, 'USD', '7.89')"
        ];

        $this->assertSame($expectedLog, $pdo->getLog());
    }
}
