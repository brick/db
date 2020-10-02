<?php

declare(strict_types=1);

namespace Brick\Db\Logger;

class DebugStatement
{
    private string $statement;

    private array $parameters;

    private float $time;

    /**
     * @param string $statement
     * @param array  $parameters
     * @param float  $time
     */
    public function __construct(string $statement, array $parameters, float $time)
    {
        $this->statement  = $statement;
        $this->parameters = $parameters;
        $this->time       = $time;
    }

    /**
     * @return string
     */
    public function getStatement() : string
    {
        return $this->statement;
    }

    /**
     * @return array
     */
    public function getParameters() : array
    {
        return $this->parameters;
    }

    /**
     * @return float
     */
    public function getTime() : float
    {
        return $this->time;
    }
}