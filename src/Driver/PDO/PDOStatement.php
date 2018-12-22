<?php

declare(strict_types=1);

namespace Brick\Db\Driver\PDO;

use Brick\Db\Driver\Statement;

class PDOStatement implements Statement
{
    /**
     * The wrapped PDO statement.
     *
     * @var \PDOStatement
     */
    protected $pdoStatement;

    /**
     * PDOStatement constructor.
     *
     * @param \PDOStatement $pdoStatement The wrapped PDO statement.
     */
    public function __construct(\PDOStatement $pdoStatement)
    {
        $this->pdoStatement = $pdoStatement;
    }

    /**
     * @inheritdoc
     */
    public function fetch(bool $assoc = false) : ?array
    {
        try {
            $result = @ $this->pdoStatement->fetch($assoc ? \PDO::FETCH_ASSOC : \PDO::FETCH_NUM);
        } catch (\PDOException $e) {
            throw PDOConnection::exceptionFromPDOException($e);
        }

        if ($result === false) {
            return null;
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
            throw PDOConnection::exceptionFromPDOStatement($this->pdoStatement);
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
            throw PDOConnection::exceptionFromPDOStatement($this->pdoStatement);
        }
    }
}
