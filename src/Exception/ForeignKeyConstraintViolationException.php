<?php

declare(strict_types=1);

namespace Brick\Db\Exception;

use Brick\Db\DbException;

/**
 * A foreign key constraint failed.
 */
class ForeignKeyConstraintViolationException extends DbException
{
}
