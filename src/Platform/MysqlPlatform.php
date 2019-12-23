<?php

declare(strict_types=1);

namespace Brick\Db\Platform;

use Brick\Db\DbException;
use Brick\Db\Driver\DriverException;
use Brick\Db\Exception;
use Brick\Db\Platform;

class MysqlPlatform extends Platform
{
    public function getExceptionClass(DriverException $e): string
    {
        switch ($e->getErrorCode()) {
            case 1205: // ER_LOCK_WAIT_TIMEOUT
                return Exception\LockWaitTimeoutException::class;

            case 1213: // ER_LOCK_DEADLOCK
                return Exception\DeadlockException::class;

            case 1062: // ER_DUP_ENTRY
            case 1169: // ER_DUP_UNIQUE
                return Exception\UniqueConstraintViolationException::class;

            default:
                return DbException::class;
        }
    }
}
