<?php

namespace Brick\Db\Tests\Bulk;

/**
 * Mocks a PDO connection for unit testing.
 */
class PDOMock extends \PDO
{
    /**
     * @var int
     */
    private $statementNumber = 0;

    /**
     * @var string[]
     */
    private $log = [];

    /**
     * Empty constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param string     $statement
     * @param array|null $options
     *
     * @return PDOStatementMock
     */
    public function prepare($statement, $options = null)
    {
        $this->log('PREPARE ' . $statement);

        return new PDOStatementMock($this, ++$this->statementNumber);
    }

    /**
     * @param string $info
     *
     * @return void
     */
    public function log(string $info) : void
    {
        $this->log[] = $info;
    }

    /**
     * @return string[]
     */
    public function getLog() : array
    {
        return $this->log;
    }
}
