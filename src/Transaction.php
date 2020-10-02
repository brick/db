<?php

declare(strict_types=1);

namespace Brick\Db;

class Transaction
{
    private TransactionManager $transactionManager;

    private int $nestingLevel;

    /**
     * @var bool
     */
    private bool $isCommitted = false;

    /**
     * @var bool
     */
    private bool $isRolledBack = false;

    /**
     * Transaction constructor.
     *
     * @param TransactionManager $transactionManager
     * @param int                $nestingLevel
     */
    public function __construct(TransactionManager $transactionManager, int $nestingLevel)
    {
        $this->transactionManager = $transactionManager;
        $this->nestingLevel       = $nestingLevel;
    }

    /**
     * @return void
     *
     * @throws DbException
     */
    public function commit() : void
    {
        $this->checkActive();
        $this->transactionManager->commit($this->nestingLevel);
        $this->isCommitted = true;
    }

    /**
     * @return void
     *
     * @throws DbException
     */
    public function rollBack() : void
    {
        $this->checkActive();
        $this->transactionManager->rollBack($this->nestingLevel);
        $this->isRolledBack = true;
    }

    /**
     * @return void
     *
     * @throws DbException
     */
    private function checkActive() : void
    {
        if ($this->isCommitted) {
            throw new DbException('Transaction has already been committed.');
        }

        if ($this->isRolledBack) {
            throw new DbException('Transaction has already been rolled back.');
        }
    }
}
