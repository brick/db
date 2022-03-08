<?php

declare(strict_types=1);

namespace Brick\Db\Interfaces;

interface Result
{
    public function rowCount(): int;
}
