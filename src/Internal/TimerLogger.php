<?php

declare(strict_types=1);

namespace Brick\Db\Internal;

use Brick\Db\Logger;

/**
 * @internal
 */
class TimerLogger
{
    /**
     * @var Logger|null
     */
    private $logger;

    /**
     * @var string|null
     */
    private $statement;

    /**
     * @var array|null
     */
    private $parameters;

    /**
     * @var float|null
     */
    private $startTime;

    /**
     * @param Logger|null $logger
     */
    public function __construct(?Logger $logger)
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
