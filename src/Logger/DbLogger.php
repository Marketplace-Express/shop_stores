<?php
/**
 * User: Wajdi Jurry
 * Date: ١‏/٥‏/٢٠٢٠
 * Time: ٣:٣٤ م
 */

namespace App\Logger;


use Doctrine\DBAL\Logging\SQLLogger;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\File\File;

class DbLogger extends AbstractProcessingHandler
{
    const LOG_NAME = 'db.log';

    /**
     * @var array
     */
    private $data = [];

    private $stream;

    public function __construct($level = Logger::DEBUG, bool $bubble = true)
    {
        $this->stream = fopen($_SERVER['LOG_DIRECTORY'] . 'db.log', 'a');
        parent::__construct($level, $bubble);
    }

    private function getQueryExecutionTime()
    {
        return $this->data['executionTime(ms)'] = microtime(true) - $this->data['start'];
    }

    /**
     * @param array $record
     */
    protected function write(array $record): void
    {
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