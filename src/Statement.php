<?php

declare(strict_types=1);

namespace Brick\Db;

interface Statement
{
    /**
     * Fetches the next row in the current result set.
     *
     * @param bool $assoc True to return the result as an associative array, false for a numeric array (default).
     *
     * @return array|null A numeric or associative array of column values, or null if no more rows.
     *
     * @throws DbException If an error occurs.
     */
    public function fetch(bool $assoc = false) : ?array;

    /**
     * Returns an array containing all of the remaining rows in the current result set.
     *
     * The resulting numeric array represents each row as an array of column values.
     * Each row is either a numeric array, or an associative array with keys corresponding to each column name.
     * An empty array is returned if there are zero results to fetch.
     *
     * @param bool $assoc True to return each row as an associative array, false for a numeric array (default).
     *
     * @return array A numeric array, where each element is a numeric or associative array of column values.
     *
     * @throws DbException If an error occurs.
     */
    public function fetchAll(bool $assoc = false) : array;

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
    public function rowCount() : int;

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
    public function nextRowset() : bool;

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
    public function closeCursor() : void;
}
