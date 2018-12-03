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
     * The PDO connection.
     *
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
            $result = @ $this->pdo->beginTransaction();
        } catch (\PDOException $e) {
            throw self::exceptionFromPDOException($e);
        }

        if ($result === false) {
            throw self::exceptionFromPDO($this->pdo);
        }
    }

    /**
     * @inheritdoc
     */
    public function commit() : void
    {
        try {
            $result = @ $this->pdo->commit();
        } catch (\PDOException $e) {
            throw self::exceptionFromPDOException($e);
        }

        if ($result === false) {
            throw self::exceptionFromPDO($this->pdo);
        }
    }

    /**
     * @inheritdoc
     */
    public function rollBack() : void
    {
        try {
            $result = @ $this->pdo->rollBack();
        } catch (\PDOException $e) {
            throw self::exceptionFromPDOException($e);
        }

        if ($result === false) {
            throw self::exceptionFromPDO($this->pdo);
        }
    }

    /**
     * @inheritdoc
     */
    public function prepare(string $statement) : PreparedStatement
    {
        try {
            $result = @ $this->pdo->prepare($statement);
        } catch (\PDOException $e) {
            throw self::exceptionFromPDOException($e);
        }

        if ($result === false) {
            throw self::exceptionFromPDO($this->pdo);
        }

        return new PDOPreparedStatement($result);
    }

    /**
     * @inheritdoc
     */
    public function query(string $statement, array $parameters = []) : Statement
    {
        if ($parameters) {
            $statement = $this->prepare($statement);
            $statement->execute($parameters);

            return $statement;
        }

        try {
            $result = @ $this->pdo->query($statement);
        } catch (\PDOException $e) {
            throw self::exceptionFromPDOException($e);
        }

        if ($result === false) {
            throw self::exceptionFromPDO($this->pdo);
        }

        return new PDOStatement($result);
    }

    /**
     * @inheritdoc
     */
    public function exec(string $statement) : int
    {
        try {
            $result = @ $this->pdo->exec($statement);
        } catch (\PDOException $e) {
            throw self::exceptionFromPDOException($e);
        }

        if ($result === false) {
            throw self::exceptionFromPDO($this->pdo);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function lastInsertId(string $name = null) : int
    {
        $lastInsertId = @ $this->pdo->lastInsertId($name);

        return (int) $lastInsertId;
    }

    /**
     * Creates an DbException from the PDO last error info.
     *
     * @param \PDO $pdo
     *
     * @return DbException
     */
    public static function exceptionFromPDO(\PDO $pdo) : DbException
    {
        return self::exceptionFromErrorInfo($pdo->errorInfo());
    }

    /**
     * Creates an DbException from a PDOStatement last error info.
     *
     * @param \PDOStatement $pdoStatement
     *
     * @return DbException
     */
    public static function exceptionFromPDOStatement(\PDOStatement $pdoStatement) : DbException
    {
        return self::exceptionFromErrorInfo($pdoStatement->errorInfo());
    }

    /**
     * Creates an DbException from a PDOException.
     *
     * @param \PDOException $pdoException
     *
     * @return DbException
     */
    public static function exceptionFromPDOException(\PDOException $pdoException) : DbException
    {
        return self::exceptionFromErrorInfo($pdoException->errorInfo, $pdoException);
    }

    /**
     * Creates an DbException from a PDO errorInfo array.
     *
     * Note: PDOException::$errorInfo can contain NULL, for example when committing a non-existing transaction on MySQL.
     * This is not documented on the php.net website.
     *
     * @param array|null         $errorInfo    The errorInfo array from PDO, PDOStatement or PDOException,
     *                                         or NULL if not available.
     * @param \PDOException|null $pdoException The PDO exception, if any.
     *
     * @return DbException
     */
    private static function exceptionFromErrorInfo(?array $errorInfo, ?\PDOException $pdoException = null) : DbException
    {
        if ($errorInfo === null) {
            $errorInfo = ['00000', null, null];
        }

        [$sqlState, $errorCode, $errorMessage] = $errorInfo;

        if ($errorMessage === null) {
            $errorMessage = $pdoException ? $pdoException->getMessage() : 'Unknown PDO error.';
        }

        return new DbException($errorMessage, $sqlState, $errorCode, $pdoException);
    }
}
