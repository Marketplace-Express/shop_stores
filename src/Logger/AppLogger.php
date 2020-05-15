<?php
/**
 * User: Wajdi Jurry
 * Date: ١٥‏/٥‏/٢٠٢٠
 * Time: ١:٥٦ ص
 */

namespace App\Logger;


use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class AppLogger extends AbstractProcessingHandler
{
    /** @var resource */
    private $stream;

    /**
     * AppLogger constructor.
     *
     * @param int $level
     * @param bool $bubble
     * @param string $rootDir
     */
    public function __construct($level = Logger::DEBUG, bool $bubble = true, string $rootDir = '/')
    {
        $this->stream = fopen($rootDir . '/logs/app.log', 'a');
        parent::__construct($level, $bubble);
    }

    /**
     * @param array $logInfo
     */
    protected function write(array $logInfo): void
    {
        if (!is_resource($this->stream)) {
            throw new \UnexpectedValueException('The stream or file could not be opened');
        }
        
        fwrite($this->stream, $logInfo['message']);
    }
}