<?php

declare(strict_types=1);

namespace Brick\Db\Bulk;

/**
 * Inserts rows into a database table in bulk.
 */
class BulkInserter extends BulkOperator
{
    /**
     * @inheritdoc
     */
    protected function getQuery(int $numRecords) : string
    {
        $fields       = implode(', ', $this->fields);
        $placeholders = implode(', ', array_fill(0, $this->numFields, '?'));

        $query  = 'INSERT INTO ' . $this->table . ' (' . $fields . ') VALUES (' . $placeholders . ')';
        $query .= str_repeat(', (' . $placeholders . ')', $numRecords - 1);

        return $query;
    }
}
