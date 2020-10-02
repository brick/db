<?php

declare(strict_types=1);

namespace Brick\Db;

/**
 * @internal This class is an implementation detail, and not part of the public API.
 */
class TransactionManager
{
    private const SAVEPOINT_PREFIX = 'BRICK_DB_SAVEPOINT_';

    private Connection $connection;

    /**
     * The current transaction nesting level. When no transaction is active, this value is zero.
     */
    private int $nestingLevel = 0;

    /**
     * Whether a transaction management statement has failed, in which case the transaction is in an unknown state.
     */
    private bool $isErrored = false;

    /**
     * TransactionManager constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Transaction
     *
     * @throws DbException
     */
    public function begin() : Transaction
    {
        if ($this->isErrored) {
            throw new DbException('Transaction cannot be started: a previous transaction management statement has errored.');
        }

        if ($this->nestingLevel === 0) {
            $statement = 'BEGIN';
        } else {
            $statement = 'SAVEPOINT ' . self::SAVEPOINT_PREFIX . $this->nestingLevel;
        }

        $this->isErrored = true;
        $this->connection->exec($statement);
        $this->isErrored = false;

        return new Transaction($this, ++$this->nestingLevel);
    }

    /**
     * @param int $nestingLevel The nesting level of the transaction being committed.
     *
     * @return void
     *
     * @throws DbException
     */
    public function commit(int $nestingLevel) : void
    {
        if ($this->isErrored) {
            throw new DbException(
                'Transaction cannot be committed: ' .
                'a previous transaction management statement has errored.'
            );
        }

        if ($nestingLevel !== $this->nestingLevel) {
            throw new DbException('Transaction cannot be committed: a nested transaction is still active.');
        }

        $this->isErrored = true;

        if ($this->nestingLevel === 1) {
            $this->connection->exec('COMMIT');
        } else {
            $savepoint = self::SAVEPOINT_PREFIX . ($this->nestingLevel - 1);
            $this->connection->exec('RELEASE SAVEPOINT ' . $savepoint);
        }

        $this->isErrored = false;

        $this->nestingLevel--;
    }

    /**
     * @param int $nestingLevel The nesting level of the transaction being rolled back.
     *
     * @return void
     *
     * @throws DbException
     */
    public function rollBack(int $nestingLevel) : void
    {
        if ($this->isErrored) {
            throw new DbException(
                'Transaction cannot be rolled back: ' .
                'a previous transaction management statement has errored.'
            );
        }

        if ($nestingLevel !== $this->nestingLevel) {
            throw new DbException('Transaction cannot be rolled back: a nested transaction is still active.');
        }

        $this->isErrored = true;

        if ($this->nestingLevel === 1) {
            $this->connection->exec('ROLLBACK');
        } else {
            $savepoint = self::SAVEPOINT_PREFIX . ($this->nestingLevel - 1);
            $this->connection->exec('ROLLBACK TO SAVEPOINT ' . $savepoint);
            $this->connection->exec('RELEASE SAVEPOINT ' . $savepoint);
        }

        $this->isErrored = false;

        $this->nestingLevel--;
    }
}
