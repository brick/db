<?php

declare(strict_types=1);

namespace Brick\Db\Tests\Bulk;

/**
 * Mocks a PDO connection for unit testing.
 */
class PDOMock extends \PDO
{
    private int $statementNumber = 0;

    /**
     * @var string[]
     */
    private array $log = [];

    /**
     * The values that will be returned by successive calls to PDOStatementMock::rowCount().
     *
     * @var int[]
     */
    private array $rowCounts;

    /**
     * @param int[] $rowCounts The values that will be returned by successive calls to PDOStatementMock::rowCount().
     *                         If no values are provided, PDOStatementMock::rowCount() will return 0.
     */
    public function __construct(array $rowCounts = [])
    {
        $this->rowCounts = $rowCounts;
    }

    public function prepare(string $statement, array $options = []) : PDOStatementMock
    {
        $statementNumber = ++$this->statementNumber;

        $this->log('PREPARE STATEMENT ' . $statementNumber . ': ' . $statement);

        return new PDOStatementMock($this, $statementNumber);
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
     * Returns the value that will be returned by PDOStatementMock::rowCount().
     */
    public function getRowCount() : int
    {
        $rowCount = array_shift($this->rowCounts);

        return $rowCount ?? 0;
    }
}
