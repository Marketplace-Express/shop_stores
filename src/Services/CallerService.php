<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/30
 * Time: 09:57
 */

namespace App\Services;


use App\Enums\QueueNamesEnum;
use Jurry\RabbitMQ\Handler\RequestSender;

class CallerService
{
    /**
     * @var RequestSender
     */
    private $requestSender;

    /**
     * AsyncCallerService constructor.
     * @param RequestSender $requestSender
     */
    public function __construct(RequestSender $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function callSync(string $route, string $method, array $body = [], array $query = [], array $headers = [])
    {
        return $this->requestSender
            ->setQueueName(QueueNamesEnum::SYNC_QUEUE_NAME)
            ->setRoute($route)
            ->setMethod($method)
            ->setHeaders($headers)
            ->setBody($body)
            ->setQuery($query)
            ->sendSync();
    }

    public function callAsync(string $route, string $method, array $body = [], array $query = [], array $headers = [])
    {
        $this->requestSender
            ->setQueueName(QueueNamesEnum::ASYNC_QUEUE_NAME)
            ->setRoute($route)
            ->setMethod($method)
            ->setHeaders($headers)
            ->setBody($body)
            ->setQuery($query)
            ->sendAsync();
    }
}