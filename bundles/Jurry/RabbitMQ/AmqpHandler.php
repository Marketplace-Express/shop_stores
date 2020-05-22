<?php
/**
 * User: Wajdi Jurry
 * Date: 22 May 2020
 * Time: 03:58 PM
 */

namespace Jurry\RabbitMQ;


use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Wire\AMQPTable;

class AmqpHandler
{
    /** @var AMQPChannel */
    private $channel;

    /** @var array */
    private $queuesProperties;

    /**
     * QueuesHandler constructor.
     * @param string $host
     * @param int $port
     * @param string $user
     * @param string $password
     * @param array $queuesProperties
     */
    public function __construct(string $host, int $port, string $user = 'guest', string $password = 'guest', array $queuesProperties = [])
    {
        $connection = new AMQPStreamConnection($host, $port, $user, $password);
        $this->channel = $connection->channel();
        $this->queuesProperties = $queuesProperties;
    }

    /**
     * @return AMQPChannel
     */
    public function getChannel(): AMQPChannel
    {
        return $this->channel;
    }

    public function declareSync()
    {
        $options = $this->queuesProperties['sync_queue'];

        $this->channel->queue_declare($options['name'],
            $options['passive'], $options['durable'], $options['exclusive'], $options['auto_delete'], $options['no_wait'],
            new AMQPTable(['x-message-ttl' => $options['message_ttl']])
        );
    }

    public function declareAsync()
    {
        $options = $this->queuesProperties['async_queue'];

        $this->channel->queue_declare($options['name'],
            $options['passive'], $options['durable'], $options['exclusive'], $options['auto_delete'], $options['no_wait'],
            new AMQPTable(['x-message-ttl' => $options['message_ttl']])
        );
    }
}