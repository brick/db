<?php

declare(strict_types=1);

namespace Brick\Db\Driver;

interface Connection
{
    /**
     * @return void
     *
     * @throws DriverException
     */
    public function beginTransaction() : void;

    /**
     * @return void
     *
     * @throws DriverException
     */
    public function commit() : void;

    /**
     * @return void
     *
     * @throws DriverException
     */
    public function rollBack() : void;

    /**
     * @param string $statement
     *
     * @return PreparedStatement
     *
     * @throws DriverException
     */
    public function prepare(string $statement) : PreparedStatement;

    /**
     * @param string $statement
     *
     * @return Statement
     *
     * @throws DriverException
     */
    public function query(string $statement) : Statement;

    /**
     * @param string $statement
     *
     * @return int
     *
     * @throws DriverException
     */
    public function exec(string $statement) : int;

    /**
     * @param string|null $name
     *
     * @return int
     *
     * @throws DriverException
     */
    public function lastInsertId(string $name = null) : int;
}
