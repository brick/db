<?php

declare(strict_types=1);

namespace Brick\Db\Exception;

use Brick\Db\DbException;

/**
 * A NOT NULL constraint violation occurred.
 *
 * Example: inserting a NULL value into a NOT NULL column.
 */
class NotNullConstraintViolationException extends DbException
{
}
