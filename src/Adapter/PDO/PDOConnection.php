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
            throw self::exceptionFromPDOException($e, $statement);
        }

        if ($result === false) {
            throw self::exceptionFromPDO($this->pdo, $statement);
        }

        return new PDOPreparedStatement($result, $statement);
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
            throw self::exceptionFromPDOException($e, $statement);
        }

        if ($result === false) {
            throw self::exceptionFromPDO($this->pdo, $statement);
        }

        return new PDOStatement($result, $statement);
    }

    /**
     * @inheritdoc
     */
    public function exec(string $statement) : int
    {
        try {
            $result = @ $this->pdo->exec($statement);
        } catch (\PDOException $e) {
            throw self::exceptionFromPDOException($e, $statement);
        }

        if ($result === false) {
            throw self::exceptionFromPDO($this->pdo, $statement);
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
     * Creates a DbException from the PDO last error info.
     *
     * @param \PDO        $pdo          The PDO connection.
     * @param string|null $sqlStatement The SQL statement that generated an exception, if any.
     *
     * @return DbException
     */
    public static function exceptionFromPDO(\PDO $pdo, ?string $sqlStatement = null) : DbException
    {
        return self::exceptionFromErrorInfo($pdo->errorInfo(), $sqlStatement);
    }

    /**
     * Creates a DbException from a PDOStatement last error info.
     *
     * @param \PDOStatement $pdoStatement The PDO statement.
     * @param string|null   $sqlStatement The SQL statement that generated an exception, if any.
     *
     * @return DbException
     */
    public static function exceptionFromPDOStatement(\PDOStatement $pdoStatement, ?string $sqlStatement = null) : DbException
    {
        return self::exceptionFromErrorInfo($pdoStatement->errorInfo(), $sqlStatement);
    }

    /**
     * Creates a DbException from a PDOException.
     *
     * @param \PDOException $pdoException The PDO exception.
     * @param string|null   $sqlStatement The SQL statement that generated an exception, if any.
     *
     * @return DbException
     */
    public static function exceptionFromPDOException(\PDOException $pdoException, ?string $sqlStatement = null) : DbException
    {
        return self::exceptionFromErrorInfo($pdoException->errorInfo, $pdoException, $sqlStatement);
    }

    /**
     * Creates a DbException from a PDO errorInfo array.
     *
     * Note: PDOException::$errorInfo can contain NULL, for example when committing a non-existing transaction on MySQL.
     * This is not documented on the php.net website.
     *
     * @param array|null         $errorInfo    The errorInfo array from PDO, PDOStatement or PDOException,
     *                                         or NULL if not available.
     * @param \PDOException|null $pdoException The PDO exception, if any.
     * @param string|null        $sqlStatement The SQL statement that generated an exception, if any.
     *
     * @return DbException
     */
    private static function exceptionFromErrorInfo(?array $errorInfo, ?\PDOException $pdoException = null, ?string $sqlStatement = null) : DbException
    {
        if ($errorInfo === null) {
            $errorInfo = ['00000', null, null];
        }

        [$sqlState, $errorCode, $errorMessage] = $errorInfo;

        if ($errorMessage === null) {
            $errorMessage = $pdoException ? $pdoException->getMessage() : 'Unknown PDO error.';
        }

        if ($sqlStatement !== null) {
            $errorMessage .= ' While executing: ' . $sqlStatement;
        }

        return new DbException($errorMessage, $sqlStatement, $sqlState, $errorCode, $pdoException);
    }
}
