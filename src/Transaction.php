<?php

declare(strict_types=1);

namespace Brick\Db;

class Transaction
{
    /**
     * @var TransactionManager
     */
    private $transactionManager;

    /**
     * @var int
     */
    private $nestingLevel;

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
     * @var bool
     */
    private $isCommitted = false;

    /**
     * @var bool
     */
    private $isRolledBack = false;

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
