<?php

declare(strict_types=1);

namespace Brick\Db\Logger;

use Brick\Db\Logger;

class DebugLogger implements Logger
{
    /**
     * @var DebugStatement[]
     */
    private $debugStatements = [];

    /**
     * {@inheritdoc}
     */
    public function log(string $statement, array $parameters, float $time) : void
    {
        $this->debugStatements[] = new DebugStatement($statement, $parameters, $time);
    }

    /**
     * Returns the statement by the given index.
     *
     * The index may be positive or negative:
     *   -  0 returns the first statement
     *   -  1 returns the second statement
     *   - -1 returns the last statement
     *   - -2 returns the next-to-last statement
     *
     * @param int $index A positive or negative index.
     *
     * @return DebugStatement
     */
    public function getDebugStatement(int $index) : DebugStatement
    {
        if ($index < 0) {
            $index = count($this->debugStatements) + $index;
        }

        if (! isset($this->debugStatements[$index])) {
            throw new \InvalidArgumentException(sprintf('No debug statement at index %d.', $index));
        }

        return $this->debugStatements[$index];
    }

    /**
     * @return void
     */
    public function reset() : void
    {
        $this->debugStatements = [];
    }
}
