<?php

declare(strict_types=1);

namespace Brick\Db\PDO;

use Brick\Db\Interfaces;
use Exception;

final class Result implements Interfaces\Result
{
    private Statement $statement;

    private int $version;

    public function __construct(Statement $statement, int $version)
    {
        $this->statement = $statement;
        $this->version = $version;
    }

    public function rowCount(): int
    {
        $this->checkVersion();

        return $this->statement->getPDOStatement()->rowCount();
    }

    private function checkVersion(): void
    {
        if ($this->version !== $this->statement->getVersion()) {
            throw new Exception(
                'Cannot access this Result because the Statement has been executed again ' .
                'since this Result was created.'
            );
        }
    }
}
