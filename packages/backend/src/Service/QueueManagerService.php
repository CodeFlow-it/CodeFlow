<?php

namespace App\Service;

use PhpAmqpLib\Message\AMQPMessage;
use App\Service\RabbitMQConnectionService;

/**
 * Service for managing queue and message
 */
class QueueManagerService
{
    /**
     * @var RabbitMQConnectionService
     */
    private RabbitMQConnectionService $rabbitMQConnectionService;

    private string $queueName;

    /**
     * QueueManagerService constructor
     * 
     * @param RabbitMQConnectionService $rabbitMQConnectionService
     */
    public function __construct(RabbitMQConnectionService $rabbitMQConnectionService)
    {
        $this->rabbitMQConnectionService = $rabbitMQConnectionService;
    }

    /**
     * Create a new queue
     *
     * @param string $queueName
     * @return void
     */
    public function createQueue(string $queueName): void
    {
        $this->queueName = $queueName;
        $channel = $this->rabbitMQConnectionService->getConnection()->channel();
        $channel->queue_declare($queueName, false, false, false, false);
        $channel->close();
        $this->rabbitMQConnectionService->getConnection()->close();
    }

    /**
     * Publish a message to a queue
     *
     * @param string $queueName
     * @param string $messageData
     * @return void
     */
    public function publishMessage(string $queueName, string $messageData): void
    {
        $channel = $this->rabbitMQConnectionService->getConnection()->channel();
        $message = new AMQPMessage($messageData);
        $channel->basic_publish($message, '', $queueName);
        $channel->close();
        $this->rabbitMQConnectionService->getConnection()->close();
    }

    /**
     * Queue name getter
     * 
     * @return string The name of the queue
     */
    public function getQueueName(): string
    {
        return $this->queueName;
    }
}
