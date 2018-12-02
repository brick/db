<?php

declare(strict_types=1);

namespace Brick\Db\Adapter\PDO;

use Brick\Db\Statement;

class PDOStatement implements Statement
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @var \PDOStatement
     */
    protected $pdoStatement;

    /**
     * PDOStatement constructor.
     *
     * @param \PDO          $pdo
     * @param \PDOStatement $pdoStatement
     */
    public function __construct(\PDO $pdo, \PDOStatement $pdoStatement)
    {
        $this->pdo          = $pdo;
        $this->pdoStatement = $pdoStatement;
    }

    /**
     * @inheritdoc
     */
    public function fetch(bool $assoc = false) : array
    {
        try {
            $result = @ $this->pdoStatement->fetch($assoc ? \PDO::FETCH_ASSOC : \PDO::FETCH_NUM);
        } catch (\PDOException $e) {
            throw PDOConnection::exceptionFromPDOException($e);
        }

        if ($result === false) {
            throw PDOConnection::exceptionFromPDO($this->pdo);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function fetchAll(bool $assoc = false) : array
    {
        try {
            $result = @ $this->pdoStatement->fetchAll($assoc ? \PDO::FETCH_ASSOC : \PDO::FETCH_NUM);
        } catch (\PDOException $e) {
            throw PDOConnection::exceptionFromPDOException($e);
        }

        if ($result === false) {
            throw PDOConnection::exceptionFromPDO($this->pdo);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function rowCount() : int
    {
        return @ $this->pdoStatement->rowCount();
    }

    /**
     * @inheritdoc
     */
    public function nextRowset() : bool
    {
        return @ $this->pdoStatement->nextRowset();
    }

    /**
     * @inheritdoc
     */
    public function closeCursor() : void
    {
        try {
            $result = @ $this->pdoStatement->closeCursor();
        } catch (\PDOException $e) {
            throw PDOConnection::exceptionFromPDOException($e);
        }

        if ($result === false) {
            throw PDOConnection::exceptionFromPDO($this->pdo);
        }
    }
}
