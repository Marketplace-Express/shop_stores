<?php
/**
 * User: Wajdi Jurry
 * Date: ١‏/٥‏/٢٠٢٠
 * Time: ٣:٣٤ م
 */

namespace App\Logger;


use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class DbLogger extends AbstractProcessingHandler
{
    const MIN_THRESHOLD_PROD_MS = 100;

    /**
     * @var array
     */
    private $data = [];

    /** @var resource */
    private $stream;

    /** @var string */
    private $env;

    /**
     * DbLogger constructor.
     * @param string $env
     * @param int $level
     * @param bool $bubble
     */
    public function __construct(string $env = 'dev', $level = Logger::DEBUG, bool $bubble = true)
    {
        // TODO: get project root directory
        $this->stream = fopen($_SERVER['LOG_DIRECTORY'] . 'db.log', 'a');
        $this->env = $env;

        parent::__construct($level, $bubble);
    }

    /**
     * @return float|mixed|string
     */
    private function getQueryExecutionTime()
    {
        return microtime(true) - $this->data['start'];
    }

    /**
     * @param array $record
     */
    protected function write(array $record): void
    {
        if ($this->env == 'prod') {
            $queryTime = $this->getQueryExecutionTime();

            // Do not log if query time is less than minimum threshold in microseconds
            if ($queryTime < self::MIN_THRESHOLD_PROD_MS * 1000) {
                return;
            }
        }

        if (!is_resource($this->stream)) {
            $this->stream = null;

            throw new \UnexpectedValueException('The stream or file could not be opened');
        }

        $this->streamWrite($this->stream, $record);
    }

    /**
     * Write to stream
     * @param resource $stream
     * @param array    $record
     */
    protected function streamWrite($stream, array $record): void
    {
        fwrite($stream, (string) $record['formatted']);
    }
}