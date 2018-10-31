<?php

declare(strict_types=1);

namespace Brick\Db\Adapter\PDO;

use Brick\Db\DbException;
use Brick\Db\PreparedStatement;

class PDOPreparedStatement extends PDOStatement implements PreparedStatement
{
    /**
     * @inheritdoc
     */
    public function bind($param, $value, int $type) : void
    {
        // @todo check if this can throw an exception
        $result = @ $this->pdoStatement->bindValue($param, $value, $type);

        if ($result === false) {
            throw new DbException(); // @todo
        }
    }

    /**
     * @inheritdoc
     */
    public function execute() : void
    {
        try {
            $result = @ $this->pdoStatement->execute();
        } catch (\PDOException $e) {
            throw new DbException(); // @todo
        }

        if ($result === false) {
            throw new DbException(); // @todo
        }
    }
}
