<?php

declare(strict_types=1);

namespace Brick\Db\Exception;

use Brick\Db\DbException;

/**
 * A constraint validation occurred.
 *
 * This is a parent exception for the following exceptions:
 *
 * - NotNullConstraintViolationException
 * - UniqueConstraintViolationException
 * - ForeignKeyConstraintViolationException
 */
abstract class ConstraintViolationException extends DbException
{
}
