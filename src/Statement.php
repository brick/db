<?php

declare(strict_types=1);

namespace Brick\Db;

use Brick\Db\Driver;
use Traversable;

class Statement
{
    protected Driver\Statement $driverStatement;

    protected string $sqlStatement;

    protected Platform $platform;

    /**
     * @param Driver\Statement $driverStatement
     * @param string           $sqlStatement
     * @param Platform         $platform
     */
    public function __construct(Driver\Statement $driverStatement, string $sqlStatement, Platform $platform)
    {
        $this->driverStatement = $driverStatement;
        $this->sqlStatement    = $sqlStatement;
        $this->platform        = $platform;
    }

    /**
     * @return array<int, mixed>|null A list of column values, or null if there are no more rows.
     *
     * @throws DbException If an error occurs.
     */
    public function fetchNumeric() : array|null
    {
        try {
            return $this->driverStatement->fetch(false);
        } catch (Driver\DriverException $e) {
            throw $this->platform->convertException($e, $this->sqlStatement);
        }
    }

    /**
     * @return array<string, mixed>|null A map of column name to value, or null if there are no more rows.
     *
     * @throws DbException If an error occurs.
     */
    public function fetchAssociative() : array|null
    {
        try {
            return $this->driverStatement->fetch(true);
        } catch (Driver\DriverException $e) {
            throw $this->platform->convertException($e, $this->sqlStatement);
        }
    }

    /**
     * @return mixed The first column value, null if there are no more rows.
     *
     * @throws DbException If an error occurs.
     */
    public function fetchColumn() : mixed
    {
        return $this->fetchNumeric()[0];
    }

    /**
     * @return array<int, array<int, mixed>> A list whose each element is a list of column values.
     *
     * @throws DbException If an error occurs.
     */
    public function fetchAllNumeric() : array
    {
        try {
            return $this->driverStatement->fetchAll(false);
        } catch (Driver\DriverException $e) {
            throw $this->platform->convertException($e, $this->sqlStatement);
        }
    }

    /**
     * @return array<int, array<string, mixed>> A list whose each element is a map of column name to value.
     *
     * @throws DbException If an error occurs.
     */
    public function fetchAllAssociative() : array
    {
        try {
            return $this->driverStatement->fetchAll(true);
        } catch (Driver\DriverException $e) {
            throw $this->platform->convertException($e, $this->sqlStatement);
        }
    }

    /**
     * @return array<int, mixed> A list whose each element is the first column value.
     *
     * @throws DbException If an error occurs.
     */
    public function fetchAllColumn() : array
    {
        return array_map(static fn(array $row) : mixed => $row[0], $this->fetchAllNumeric());
    }

    /**
     * @return Traversable<int, array<int, mixed>> A traversable list whose each element is a list of column values.
     *
     * @throws DbException If an error occurs.
     */
    public function iterateNumeric() : Traversable
    {
        while (($row = $this->fetchNumeric()) !== null) {
            yield $row;
        }
    }

    /**
     * @return Traversable<int, array<int, mixed>> A traversable list whose each element is a map of column name to value.
     *
     * @throws DbException If an error occurs.
     */
    public function iterateAssociative() : Traversable
    {
        while (($row = $this->fetchNumeric()) !== null) {
            yield $row;
        }
    }

    /**
     * @return Traversable<int, mixed> A traversable list whose each element is the first column value.
     *
     * @throws DbException If an error occurs.
     */
    public function iterateColumn() : Traversable
    {
        while (($row = $this->fetchNumeric()) !== null) {
            yield $row[0];
        }
    }

    /**
     * Returns the number of rows affected by the current statement.
     *
     * If the current statement is a DELETE, INSERT or UPDATE statement, the number of rows affected is returned.
     * If the current statement is a SELECT statement, some databases *may* return the number of rows returned by that
     * statement. However, this behaviour is not guaranteed for all databases and should not be relied on for portable
     * applications.
     *
     * @return int The number of rows affected.
     *
     * @throws DbException If an error occurs.
     */
    public function rowCount() : int
    {
        try {
            return $this->driverStatement->rowCount();
        } catch (Driver\DriverException $e) {
            throw $this->platform->convertException($e, $this->sqlStatement);
        }
    }

    /**
     * Advances to the next rowset in a multi-rowset statement.
     *
     * Some database adapters support executing multiple queries in a single statement, and some database servers
     * support stored procedures that return more than one rowset (also known as a result set).
     *
     * This method enables you to access the second and subsequent rowsets associated with this statement.
     * Each rowset can have a different set of columns from the preceding rowset.
     *
     * @return bool True if there is a next statement, false if there isn't.
     *
     * @throws DbException If an error occurs.
     */
    public function nextRowset() : bool
    {
        try {
            return $this->driverStatement->nextRowset();
        } catch (Driver\DriverException $e) {
            throw $this->platform->convertException($e, $this->sqlStatement);
        }
    }

    /**
     * Closes the cursor, enabling other SQL statements to be executed, and this statement to be executed again.
     *
     * This method frees up the connection to the server so that other SQL statements may be issued, but leaves the
     * statement in a state that enables it to be executed again.
     *
     * This method should be called before executing a new statement, whenever a previously executed statement has
     * unfetched rows.
     *
     * Note that calling this method is not necessary for some database drivers, but should be called anyway when a
     * previously executed statement has unfetched rows, for portable applications.
     *
     * @return void
     *
     * @throws DbException If an error occurs.
     */
    public function closeCursor() : void
    {
        try {
            $this->driverStatement->closeCursor();
        } catch (Driver\DriverException $e) {
            throw $this->platform->convertException($e, $this->sqlStatement);
        }
    }
}
