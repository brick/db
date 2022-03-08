<?php

declare(strict_types=1);

namespace Brick\Db\Interfaces;

interface Statement
{
    public function execute(array $params): Result;
}
