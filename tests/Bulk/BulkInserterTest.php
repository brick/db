<?php

namespace Brick\Db\Tests\Bulk;

use Brick\Db\Bulk\BulkInserter;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for class BulkInserter.
 */
class BulkInserterTest extends TestCase
{
    public function testBulkInserter()
    {
        $pdo = new PDOMock();
        $bulk = new BulkInserter($pdo, 'transactions', ['user', 'currency', 'amount'], 3);

        $bulk->queue(1, 'EUR', '1.23');
        $bulk->queue(2, 'USD', '2.34');
        $bulk->queue(3, 'GBP', '3.45');
        $bulk->queue(4, 'CAD', '4.56');

        $bulk->flush();

        $expectedLog = [
            "PREPARE INSERT INTO transactions (user, currency, amount) VALUES (?, ?, ?), (?, ?, ?), (?, ?, ?)",
            "EXECUTE STATEMENT 1 (1, 'EUR', '1.23', 2, 'USD', '2.34', 3, 'GBP', '3.45')",
            "PREPARE INSERT INTO transactions (user, currency, amount) VALUES (?, ?, ?)",
            "EXECUTE STATEMENT 2 (4, 'CAD', '4.56')"
        ];

        $this->assertSame($expectedLog, $pdo->getLog());
    }
}
