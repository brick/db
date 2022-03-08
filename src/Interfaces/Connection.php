<?php

declare(strict_types=1);

namespace Brick\Db\Interfaces;

interface Connection
{
    public function prepare(string $sql): Statement;
}
