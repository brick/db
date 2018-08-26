<?php

declare(strict_types=1);

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

        $inserter = new BulkInserter($pdo, 'transaction', ['user', 'currency', 'amount'], 3);

        $this->assertSame(0, $inserter->getPendingOperations());
        $this->assertSame(0, $inserter->getAffectedRows());

        $data = [
            [[1, 'EUR', '1.23'], false, 0],
            [[2, 'USD', '2.34'], false, 0],
            [[3, 'GBP', '3.45'], true, 3],
            [[4, 'CAD', '4.56'], false, 3],
            [[5, 'USD', '5.67'], false, 3],
            [[6, 'USD', '6.78'], true, 6],
            [[7, 'USD', '7.89'], false, 6],
        ];

        $pendingOperations = 0;
        $flushedOperations = 0;
        $totalOperations = 0;

        foreach ($data as [$parameters, $isFlush, $rowCount]) {
            $result = $inserter->queue(...$parameters);
            $totalOperations++;
            $pendingOperations++;

            if ($isFlush) {
                $flushedOperations += $pendingOperations;
                $pendingOperations = 0;
            }

            $this->assertSame($result, $isFlush);
            $this->assertSame($totalOperations, $inserter->getTotalOperations());
            $this->assertSame($flushedOperations, $inserter->getFlushedOperations());
            $this->assertSame($pendingOperations, $inserter->getPendingOperations());
            $this->assertSame($rowCount, $inserter->getAffectedRows());
        }

        $inserter->flush();

        $this->assertSame(0, $inserter->getPendingOperations());
        $this->assertSame(7, $inserter->getAffectedRows());

        $expectedLog = [
            "PREPARE STATEMENT 1: INSERT INTO transaction (user, currency, amount) VALUES (?, ?, ?), (?, ?, ?), (?, ?, ?)",
            "EXECUTE STATEMENT 1: (1, 'EUR', '1.23', 2, 'USD', '2.34', 3, 'GBP', '3.45')",
            "EXECUTE STATEMENT 1: (4, 'CAD', '4.56', 5, 'USD', '5.67', 6, 'USD', '6.78')",
            "PREPARE STATEMENT 2: INSERT INTO transaction (user, currency, amount) VALUES (?, ?, ?)",
            "EXECUTE STATEMENT 2: (7, 'USD', '7.89')"
        ];

        $this->assertSame($expectedLog, $pdo->getLog());
    }

    public function testReset()
    {
        $pdo = new PDOMock([2]);
        $inserter = new BulkInserter($pdo, 'user', ['id', 'name'], 2);

        $inserter->queue(1, 'Bob');
        $inserter->queue(2, 'John');
        $inserter->queue(3, 'Alice');

        $this->assertSame(1, $inserter->getPendingOperations());
        $this->assertSame(2, $inserter->getFlushedOperations());
        $this->assertSame(3, $inserter->getTotalOperations());
        $this->assertSame(2, $inserter->getAffectedRows());

        $inserter->reset();

        $this->assertSame(0, $inserter->getPendingOperations());
        $this->assertSame(0, $inserter->getFlushedOperations());
        $this->assertSame(0, $inserter->getTotalOperations());
        $this->assertSame(0, $inserter->getAffectedRows());
    }
}
