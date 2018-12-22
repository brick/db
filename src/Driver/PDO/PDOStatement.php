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
     * The SQL statement being prepared.
     *
     * @var string
     */
    protected $sqlStatement;

    /**
     * PDOStatement constructor.
     *
     * @param \PDOStatement $pdoStatement The wrapped PDO statement.
     * @param string        $sqlStatement The SQL statement being prepared.
     */
    public function __construct(\PDOStatement $pdoStatement, string $sqlStatement)
    {
        $this->pdoStatement = $pdoStatement;
        $this->sqlStatement = $sqlStatement;
    }

    /**
     * @inheritdoc
     */
    public function fetch(bool $assoc = false) : ?array
    {
        try {
            $result = @ $this->pdoStatement->fetch($assoc ? \PDO::FETCH_ASSOC : \PDO::FETCH_NUM);
        } catch (\PDOException $e) {
            throw PDOConnection::exceptionFromPDOException($e, $this->sqlStatement);
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
            throw PDOConnection::exceptionFromPDOException($e, $this->sqlStatement);
        }

        if ($result === false) {
            throw PDOConnection::exceptionFromPDOStatement($this->pdoStatement, $this->sqlStatement);
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
            throw PDOConnection::exceptionFromPDOException($e, $this->sqlStatement);
        }

        if ($result === false) {
            throw PDOConnection::exceptionFromPDOStatement($this->pdoStatement, $this->sqlStatement);
        }
    }
}
