<?php

declare(strict_types=1);

namespace Brick\Db\Exception\ConstraintViolationException;

use Brick\Db\Exception\ConstraintViolationException;

/**
 * A NOT NULL constraint violation occurred.
 *
 * Example: inserting a NULL value into a NOT NULL column.
 */
class NotNullConstraintViolationException extends ConstraintViolationException
{
}
