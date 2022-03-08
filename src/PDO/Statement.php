<?php

declare(strict_types=1);

namespace Brick\Db\PDO;

use Brick\Db\Interfaces;
use PDOStatement;

final class Statement implements Interfaces\Statement
{
    private Connection $connection;
    private PDOStatement $pdoStatement;
    private int $version = 0;

    public function __construct(Connection $connection, PDOStatement $pdoStatement)
    {
        $this->connection = $connection;
        $this->pdoStatement = $pdoStatement;
    }

    public function execute(array $params): Result
    {
        $this->version++;

        $this->connection->wrap(
            fn () => $this->pdoStatement->execute($params),
        );

        return new Result($this, $this->version);
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function getPDOStatement(): PDOStatement
    {
        return $this->pdoStatement;
    }

    public function getVersion(): int
    {
        return $this->version;
    }
}
