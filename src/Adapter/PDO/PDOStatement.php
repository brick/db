<?php

declare(strict_types=1);

namespace Brick\Db\Adapter\PDO;

use Brick\Db\DbException;
use Brick\Db\Statement;

class PDOStatement implements Statement
{
    /**
     * @var \PDOStatement
     */
    protected $pdoStatement;

    /**
     * PDOStatement constructor.
     *
     * @param \PDOStatement $pdoStatement
     */
    public function __construct(\PDOStatement $pdoStatement)
    {
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
            throw new DbException(); // @todo
        }

        if ($result === false) {
            throw new DbException(); // @todo
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
            throw new DbException(); // @todo
        }

        if ($result === false) {
            throw new DbException(); // @todo
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
        // @todo check if this can actually throw an exception
        try {
            $result = @ $this->pdoStatement->nextRowset();
        } catch (\PDOException $e) {
            throw new DbException(); // @todo
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function closeCursor() : void
    {
        try {
            $result = @ $this->pdoStatement->closeCursor();
        } catch (\PDOException $e) {
            throw new DbException(); // @todo
        }

        if ($result === false) {
            throw new DbException(); // @todo
        }
    }
}
