<?php

declare(strict_types=1);

namespace Brick\Db\Tests\Mocks;

use Brick\Db\Interfaces\Connection;
use Brick\Db\Interfaces\Statement;

/**
 * Mocks a Connection for unit testing.
 */
final class ConnectionMock implements Connection
{
    private int $statementNumber = 0;

    /**
     * @var string[]
     */
    private array $log = [];

    /**
     * The values that will be returned by successive calls to DOStatementMock::rowCount().
     *
     * @var int[]
     */
    private array $rowCounts;

    /**
     * @param int[] $rowCounts The values that will be returned by each Result::rowCount().
     *                         If no values are provided, Result::rowCount() will return 0.
     */
    public function __construct(array $rowCounts = [])
    {
        $this->rowCounts = $rowCounts;
    }

    public function prepare(string $sql): Statement
    {
        $statementNumber = ++$this->statementNumber;

        $this->log('PREPARE STATEMENT ' . $statementNumber . ': ' . $sql);

        return new StatementMock($this, $statementNumber);
    }

    public function log(string $info) : void
    {
        $this->log[] = $info;
    }

    /**
     * @return string[]
     */
    public function getLog() : array
    {
        return $this->log;
    }

    /**
     * Returns the value that will be returned by the next Result::rowCount().
     */
    public function getRowCount() : int
    {
        $rowCount = array_shift($this->rowCounts);

        return $rowCount ?? 0;
    }
}
