<?php

declare(strict_types=1);

namespace Brick\Db\Tests\Mocks;

use Brick\Db\Interfaces\Result;

/**
 * Mocks a Result for unit testing.
 */
class ResultMock implements Result
{
    private int $rowCount;

    public function __construct(int $rowCount)
    {
        $this->rowCount = $rowCount;
    }

    public function rowCount(): int
    {
        return $this->rowCount;
    }
}
