<?php

declare(strict_types=1);

namespace Brick\Db;

use Brick\Db\Driver;

class PreparedStatement extends Statement
{
    /**
     * @var Driver\PreparedStatement
     */
    protected $driverPreparedStatement;

    /**
     * @param Driver\PreparedStatement $driverStatement
     * @param string                   $sqlStatement
     */
    public function __construct(Driver\PreparedStatement $driverStatement, string $sqlStatement)
    {
        parent::__construct($driverStatement, $sqlStatement);

        $this->driverPreparedStatement = $driverStatement;
    }

    /**
     * Executes this prepared statement.
     *
     * The parameters array must contain exactly the number of bound parameters in the SQL statement being executed,
     * otherwise an exception is thrown.
     *
     * The array can be an associative array for prepared statements using named parameters, or a 0-based numeric array
     * for prepared statements using question mark placeholders. Note that not all drivers support named parameters.
     *
     * The type of each parameter must be one of:
     *
     * - null
     * - string
     * - int
     * - float
     * - bool
     * - resource
     *
     * If a resource is given, it is read from the current position until EOF.
     * Other types (array, object) will throw an exception.
     *
     * @param array $parameters An array of values with as many elements as there are bound parameters.
     *
     * @return void
     *
     * @throws DbException
     */
    public function execute(array $parameters = []) : void
    {
        try {
            $this->driverPreparedStatement->execute($parameters);
        } catch (Driver\DriverException $e) {
            throw DbException::fromDriverException($e, $this->sqlStatement, $parameters);
        }
    }
}
