<?php 
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'user', 'password');
$channel = $connection->channel();

$channel->queue_declare('ma_queue', false, false, false, false);

echo " [*] En attente de messages. Pour quitter, pressez CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] Reçu ', $msg->body, "\n";
    // Vous pouvez ajouter ici le code pour exécuter Exakat ou d'autres scripts
};

$channel->basic_consume('ma_queue', '', false, true, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();
