<?php

declare(strict_types=1);

namespace Brick\Db;

interface Logger
{
    /**
     * @param string $statement  The SQL statement.
     * @param array  $parameters The bound parameters.
     * @param float  $time       The time it took to execute the statement, in seconds.
     *
     * @return void
     */
    public function log(string $statement, array $parameters, float $time) : void;
}
