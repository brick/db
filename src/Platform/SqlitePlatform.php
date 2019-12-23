<?php

declare(strict_types=1);

namespace Brick\Db\Platform;

use Brick\Db\DbException;
use Brick\Db\Driver\DriverException;
use Brick\Db\Exception;
use Brick\Db\Platform;

class SqlitePlatform extends Platform
{
    public function getExceptionClass(DriverException $e): string
    {
        $message = $e->getMessage();

        switch ($e->getErrorCode()) {
            case 5: // SQLITE_BUSY
            case 6: // SQLITE_LOCKED
                return Exception\LockWaitTimeoutException::class;

            case 19: // SQLITE_CONSTRAINT
                if ($this->startsWith($message, 'NOT NULL constraint failed')) {
                    return Exception\NotNullConstraintViolationException::class;
                }
                if ($this->startsWith($message, 'UNIQUE constraint failed')) {
                    return Exception\UniqueConstraintViolationException::class;
                }
                if ($this->startsWith($message, 'FOREIGN KEY constraint failed')) {
                    return Exception\ForeignKeyConstraintViolationException::class;
                }
                break;
        }

        return DbException::class;
    }

    private function startsWith(string $message, string $substring): bool
    {
        return strpos($message, $substring) === 0;
    }
}
