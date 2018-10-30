<?php

declare(strict_types=1);

namespace Brick\Db\Adapter\PDO;

use Brick\Db\Connection;
use Brick\Db\DbException;
use Brick\Db\PreparedStatement;
use Brick\Db\Statement;

class PDOConnection implements Connection
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * PDOConnection constructor.
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @inheritdoc
     */
    public function beginTransaction() : void
    {
        try {
            $result = $this->pdo->beginTransaction();
        } catch (\PDOException $e) {
            throw new DbException(); // @todo
        }

        if ($result === false) {
            throw new DbException(); // @todo
        }
    }

    /**
     * @inheritdoc
     */
    public function commit() : void
    {
        try {
            $result = $this->pdo->commit();
        } catch (\PDOException $e) {
            throw new DbException(); // @todo
        }

        if ($result === false) {
            throw new DbException(); // @todo
        }
    }

    /**
     * @inheritdoc
     */
    public function rollBack() : void
    {
        try {
            $result = $this->pdo->rollBack();
        } catch (\PDOException $e) {
            throw new DbException(); // @todo
        }

        if ($result === false) {
            throw new DbException(); // @todo
        }
    }

    /**
     * @inheritdoc
     */
    public function prepare(string $statement) : PreparedStatement
    {
        try {
            $result = $this->pdo->prepare($statement);
        } catch (\PDOException $e) {
            throw new DbException(); // @todo
        }

        if ($result === false) {
            throw new DbException(); // @todo
        }

        return new PDOPreparedStatement($result);
    }

    /**
     * @inheritdoc
     */
    public function query(string $statement) : Statement
    {
        try {
            $result = $this->pdo->query($statement);
        } catch (\PDOException $e) {
            throw new DbException(); // @todo
        }

        if ($result === false) {
            throw new DbException(); // @todo
        }

        return new PDOStatement($result);
    }

    /**
     * @inheritdoc
     */
    public function exec(string $statement) : int
    {
        try {
            $result = $this->pdo->exec($statement);
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
    public function lastInsertId(string $name = null) : int
    {
        // @todo check that it actually throws an exception, and never returns false
        try {
            $lastInsertId = $this->pdo->lastInsertId($name);
        } catch (\PDOException $e) {
            throw new DbException(); // @todo
        }

        return (int) $lastInsertId;
    }
}
