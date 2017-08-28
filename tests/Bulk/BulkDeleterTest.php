<?php

namespace Brick\Db\Tests\Bulk;

use Brick\Db\Bulk\BulkDeleter;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for class BulkDeleter.
 */
class BulkDeleterTest extends TestCase
{
    public function testBulkDeleter()
    {
        $pdo = new PDOMock();
        $deleter = new BulkDeleter($pdo, 'transactions', ['store_id', 'transaction_number'], 3);

        $deleter->queue(1, 1);
        $deleter->queue(1, 2);
        $deleter->queue(2, 100);
        $deleter->queue(2, 101);

        $deleter->flush();

        $expectedLog = [
            "PREPARE DELETE FROM transactions WHERE (store_id = ? AND transaction_number = ?) OR (store_id = ? AND transaction_number = ?) OR (store_id = ? AND transaction_number = ?)",
            "EXECUTE STATEMENT 1 (1, 1, 1, 2, 2, 100)",
            "PREPARE DELETE FROM transactions WHERE (store_id = ? AND transaction_number = ?)",
            "EXECUTE STATEMENT 2 (2, 101)"
        ];

        $this->assertSame($expectedLog, $pdo->getLog());
    }
}
