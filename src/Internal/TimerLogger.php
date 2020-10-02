<?php

declare(strict_types=1);

namespace Brick\Db\Internal;

use Brick\Db\Logger;

/**
 * @internal
 */
class TimerLogger
{
    private Logger|null $logger;

    private string|null $statement;

    private array|null $parameters;

    private float|null $startTime;

    public function __construct(Logger|null $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $statement  The SQL statement.
     * @param array  $parameters The bound parameters.
     *
     * @return void
     */
    public function start(string $statement, array $parameters = []) : void
    {
        if ($this->logger === null) {
            return;
        }

        $this->statement  = $statement;
        $this->parameters = $parameters;
        $this->startTime  = microtime(true);
    }

    /**
     * @return void
     */
    public function stop() : void
    {
        if ($this->logger === null) {
            return;
        }

        $time = microtime(true) - $this->startTime;

        $this->logger->log(
            $this->statement,
            $this->parameters,
            $time
        );

        $this->statement  = null;
        $this->parameters = null;
        $this->startTime  = null;
    }
}
