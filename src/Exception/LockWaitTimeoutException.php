<?php

declare(strict_types=1);

namespace Brick\Db\Exception;

use Brick\Db\DbException;

/**
 * The lock wait timeout has been exceeded. The transaction may be retried.
 */
class LockWaitTimeoutException extends DbException
{
}
