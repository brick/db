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
            case 1036: // ER_OPEN_AS_READONLY
                return Exception\ReadOnlyException::class;

            case 1205: // ER_LOCK_WAIT_TIMEOUT
                return Exception\LockWaitTimeoutException::class;

            case 1213: // ER_LOCK_DEADLOCK
                return Exception\DeadlockException::class;

            case 1062: // ER_DUP_ENTRY
            case 1169: // ER_DUP_UNIQUE
                return Exception\ConstraintViolationException\UniqueConstraintViolationException::class;

            case 1216: // ER_NO_REFERENCED_ROW
            case 1217: // ER_ROW_IS_REFERENCED
            case 1451: // ER_ROW_IS_REFERENCED_2
            case 1452: // ER_NO_REFERENCED_ROW_2
            case 1701: // ER_TRUNCATE_ILLEGAL_FK
                return Exception\ConstraintViolationException\ForeignKeyConstraintViolationException::class;

            case 1048: // ER_BAD_NULL_ERROR
            case 1121: // ER_NULL_COLUMN_IN_INDEX
            case 1138: // ER_INVALID_USE_OF_NULL
            case 1171: // ER_PRIMARY_CANT_HAVE_NULL
            case 1252: // ER_SPATIAL_CANT_HAVE_NULL
            case 1263: // ER_WARN_NULL_TO_NOTNULL
            case 1566: // ER_NULL_IN_VALUES_LESS_THAN
                return Exception\ConstraintViolationException\NotNullConstraintViolationException::class;

            default:
                return DbException::class;
        }
    }
}