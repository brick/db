<?php

declare(strict_types=1);

namespace Brick\Db\PDO;

use Brick\Db\Interfaces;
use Closure;
use PDO;

final class Connection implements Interfaces\Connection
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function prepare(string $sql): Statement
    {
        $pdoStatement = $this->wrap(
            fn () => $this->pdo->prepare($sql),
        );

        assert($pdoStatement !== false);

        return new Statement($this, $pdoStatement);
    }

    /**
     * @template T
     *
     * @param Closure():T $callback
     *
     * @return T
     */
    public function wrap(Closure $callback)
    {
        $errMode = $this->pdo->getAttribute(PDO::ATTR_ERRMODE);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try {
            return $callback();
        } finally {
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, $errMode);
        }
    }
}
