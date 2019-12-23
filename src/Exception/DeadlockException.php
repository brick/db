<?php

declare(strict_types=1);

namespace Brick\Db\Exception;

use Brick\Db\DbException;

/**
 * A deadlock occurred. The transaction may be retried.
 */
class DeadlockException extends DbException
{
}
