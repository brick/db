<?php

declare(strict_types=1);

namespace Brick\Db;

interface PreparedStatement extends Statement
{
    /**
     * Binds a value to a parameter.
     *
     * @param string|int $param
     * @param mixed      $value
     * @param int        $type
     *
     * @return void
     *
     * @throws DbException
     */
    public function bind($param, $value, int $type) : void;

    /**
     * Executes this prepared statement.
     *
     * @return void
     *
     * @throws DbException
     */
    public function execute() : void;
}
