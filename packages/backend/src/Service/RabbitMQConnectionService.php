<?php

namespace App\Service;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQConnectionService
{
    private string $host;
    private string $port;
    private string $user;
    private string $password;

    public function __construct(string $host, string $port, string $user, string $password)
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
    }

    public function getConnection(): AMQPStreamConnection
    {
        return new AMQPStreamConnection($this->host, $this->port, $this->user, $this->password);
    }
}
