<?php

declare(strict_types=1);

namespace Brick\Db;

interface Connection
{
    /**
     * @return void
     *
     * @throws DbException
     */
    public function beginTransaction() : void;

    /**
     * @return void
     *
     * @throws DbException
     */
    public function commit() : void;

    /**
     * @return void
     *
     * @throws DbException
     */
    public function rollBack() : void;

    /**
     * @param string $statement
     *
     * @return PreparedStatement
     *
     * @throws DbException
     */
    public function prepare(string $statement) : PreparedStatement;

    /**
     * @param string $statement
     *
     * @return Statement
     *
     * @throws DbException
     */
    public function query(string $statement) : Statement;

    /**
     * @param string $statement
     *
     * @return int
     *
     * @throws DbException
     */
    public function exec(string $statement) : int;

    /**
     * @param string|null $name
     *
     * @return int
     *
     * @throws DbException
     */
    public function lastInsertId(string $name = null) : int;
}
