<?php

declare(strict_types=1);

namespace Brick\Db\Exception;

use Brick\Db\DbException;

/**
 * A unique constraint has been violated.
 *
 * This occurs when an INSERT or UPDATE would lead to a duplicate entry, either on the primary key or a unique index.
 */
class UniqueConstraintViolationException extends DbException
{
}
