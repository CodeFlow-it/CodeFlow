<?php

namespace App\Service;

use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * Service for managing RabbitMQ connections
 */
class RabbitMQConnectionService
{
    private string $host;
    private string $port;
    private string $user;
    private string $password;

    /**
     * RabbitMQConnectionService constructor
     *
     * @param string $host
     * @param string $port
     * @param string $user
     * @param string $password
     */
    public function __construct(string $host, string $port, string $user, string $password)
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Get the AMQPStreamConnection object for the RabbitMQ connection
     *
     * @return AMQPStreamConnection
     */
    public function getConnection(): AMQPStreamConnection
    {
        return new AMQPStreamConnection($this->host, $this->port, $this->user, $this->password);
    }
}