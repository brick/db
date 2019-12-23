<?php

declare(strict_types=1);

namespace Brick\Db\Exception;

use Brick\Db\DbException;

/**
 * An attempt has been made to write to a read-only database.
 */
class ReadOnlyException extends DbException
{
}
