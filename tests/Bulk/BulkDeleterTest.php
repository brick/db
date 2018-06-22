<?php

namespace Brick\Db\Tests\Bulk;

use Brick\Db\Bulk\BulkDeleter;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for class BulkDeleter.
 */
class BulkDeleterTest extends TestCase
{
    public function testBulkDelete()
    {
        $pdo = new PDOMock([3, 4, 2]);
        $deleter = new BulkDeleter($pdo, 'transaction', ['store_id', 'transaction_number'], 3);

        $deleter->queue(1, 1);
        $deleter->queue(1, 2);
        $deleter->queue(2, 10);
        $deleter->queue(2, 11);
        $deleter->queue(3, 100);
        $deleter->queue(3, 101);
        $deleter->queue(4, 1000);

        $this->assertSame(7, $deleter->getAffectedRows());
        $this->assertSame(1, $deleter->getPendingOperations());
        $this->assertSame(6, $deleter->getFlushedOperations());
        $this->assertSame(7, $deleter->getTotalOperations());

        $deleter->flush();

        $this->assertSame(9, $deleter->getAffectedRows());
        $this->assertSame(0, $deleter->getPendingOperations());
        $this->assertSame(7, $deleter->getFlushedOperations());
        $this->assertSame(7, $deleter->getTotalOperations());

        $expectedLog = [
            "PREPARE STATEMENT 1: DELETE FROM transaction WHERE (store_id = ? AND transaction_number = ?) OR (store_id = ? AND transaction_number = ?) OR (store_id = ? AND transaction_number = ?)",
            "EXECUTE STATEMENT 1: (1, 1, 1, 2, 2, 10)",
            "EXECUTE STATEMENT 1: (2, 11, 3, 100, 3, 101)",
            "PREPARE STATEMENT 2: DELETE FROM transaction WHERE (store_id = ? AND transaction_number = ?)",
            "EXECUTE STATEMENT 2: (4, 1000)"
        ];

        $this->assertSame($expectedLog, $pdo->getLog());
    }
}
