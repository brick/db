<?php

declare(strict_types=1);

namespace Brick\Db\Tests\Mocks;

use Brick\Db\Interfaces\Result;
use Brick\Db\Interfaces\Statement;

/**
 * Mocks a Statement for unit testing.
 */
class StatementMock implements Statement
{
    private ConnectionMock $connection;

    private int $number;

    public function __construct(ConnectionMock $connection, int $number)
    {
        $this->connection = $connection;
        $this->number = $number;
    }

    public function execute(array $parameters): Result
    {
        $this->connection->log('EXECUTE STATEMENT ' . $this->number . ': (' . $this->dump($parameters) . ')');

        return new ResultMock($this->connection->getRowCount());
    }

    private function dump(array $parameters) : string
    {
        return implode(', ', array_map(
            fn ($value) => var_export($value, true),
            $parameters,
        ));
    }
}
