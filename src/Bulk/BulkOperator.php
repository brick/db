<?php

declare(strict_types=1);

namespace Brick\Db\Bulk;

use Brick\Db\Interfaces\Connection;
use Brick\Db\Interfaces\Statement;

/**
 * Base class for BulkInserter and BulkDeleter.
 */
abstract class BulkOperator
{
    /**
     * The database connection.
     */
    private Connection $connection;

    /**
     * The name of the target database table.
     */
    protected string $table;

    /**
     * The name of the fields to process.
     *
     * @var string[]
     */
    protected array $fields;

    /**
     * The number of fields above. This is to avoid redundant count() calls.
     */
    protected int $numFields;

    /**
     * The number of records to process per query.
     */
    private int $operationsPerQuery;

    /**
     * The prepared statement to process a full batch of records.
     */
    private Statement $preparedStatement;

    /**
     * A buffer containing the pending values to process in the next batch.
     */
    private array $buffer = [];

    /**
     * The number of operations in the buffer.
     */
    private int $bufferSize = 0;

    /**
     * The total number of operations that have been queued.
     *
     * This includes both flushed and pending operations.
     */
    private int $totalOperations = 0;

    /**
     * The total number of rows affected by flushed operations.
     */
    private int $affectedRows = 0;

    /**
     * @param Connection $connection         The database connection.
     * @param string     $table              The name of the table.
     * @param string[]   $fields             The name of the relevant fields.
     * @param int        $operationsPerQuery The number of operations to process in a single query.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(Connection $connection, string $table, array $fields, int $operationsPerQuery = 100)
    {
        if ($operationsPerQuery < 1) {
            throw new \InvalidArgumentException('The number of operations per query must be 1 or more.');
        }

        $numFields = count($fields);

        if ($numFields === 0) {
            throw new \InvalidArgumentException('The field list is empty.');
        }

        $this->connection = $connection;
        $this->table      = $table;
        $this->fields     = $fields;
        $this->numFields  = $numFields;

        $this->operationsPerQuery = $operationsPerQuery;

        $query = $this->getQuery($operationsPerQuery);
        $this->preparedStatement = $this->connection->prepare($query);
    }

    /**
     * Queues an operation.
     *
     * @param mixed ...$values The values to process.
     *
     * @return bool Whether a batch has been synchronized with the database.
     *              This can be used to display progress feedback.
     *
     * @throws \InvalidArgumentException If the number of values does not match the field count.
     */
    public function queue(...$values) : bool
    {
        $count = count($values);

        if ($count !== $this->numFields) {
            throw new \InvalidArgumentException(sprintf(
                'The number of values (%u) does not match the field count (%u).',
                $count,
                $this->numFields
            ));
        }

        foreach ($values as $value) {
            $this->buffer[] = $value;
        }

        $this->bufferSize++;
        $this->totalOperations++;

        if ($this->bufferSize !== $this->operationsPerQuery) {
            return false;
        }

        $result = $this->preparedStatement->execute($this->buffer);
        $this->affectedRows += $result->rowCount();

        $this->buffer = [];
        $this->bufferSize = 0;

        return true;
    }

    /**
     * Flushes the pending data to the database.
     *
     * This is to be called once after the last queue() has been processed,
     * to force flushing the remaining queued operations to the database table.
     *
     * Do *not* forget to call this method after all the operations have been queued,
     * or it could result in data loss.
     */
    public function flush() : void
    {
        if ($this->bufferSize === 0) {
            return;
        }

        $query = $this->getQuery($this->bufferSize);
        $statement = $this->connection->prepare($query);
        $result = $statement->execute($this->buffer);
        $this->affectedRows += $result->rowCount();

        $this->buffer = [];
        $this->bufferSize = 0;
    }

    /**
     * Resets the bulk operator.
     *
     * This removes any pending operations, and resets the affected row count.
     */
    public function reset() : void
    {
        $this->buffer = [];
        $this->bufferSize = 0;
        $this->affectedRows = 0;
        $this->totalOperations = 0;
    }

    /**
     * Returns the total number of operations that have been queued.
     *
     * This includes both flushed and pending operations.
     */
    public function getTotalOperations() : int
    {
        return $this->totalOperations;
    }

    /**
     * Returns the number of operations that have been flushed to the database.
     */
    public function getFlushedOperations() : int
    {
        return $this->totalOperations - $this->bufferSize;
    }

    /**
     * Returns the number of pending operations in the buffer.
     */
    public function getPendingOperations() : int
    {
        return $this->bufferSize;
    }

    /**
     * Returns the total number of rows affected by flushed operations.
     *
     * For BulkInserter, this will be equal to the number of operations flushed to the database.
     */
    public function getAffectedRows() : int
    {
        return $this->affectedRows;
    }

    abstract protected function getQuery(int $numRecords) : string;
}
