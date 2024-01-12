<?php

namespace App\Controller;

use PhpAmqpLib\Channel\AMQPChannel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use App\Service\RabbitMQConnectionService;

class JobMessageController extends AbstractController
{
    #[Route('/job/message', name: 'app_job_message')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/JobMessageController.php',
        ]);
    }

    #[Route('/job/message/send', name: 'app_job_send_message')]
    public function send(RabbitMQConnectionService $RabbitMQConnectionService): JsonResponse
    {
        $connection = $RabbitMQConnectionService->getConnection();
        $channel = $connection->channel();

        $channel->queue_declare('hello', false, false, false, false);
        $message = new AMQPMessage('hello world');

        $channel->basic_publish($message, '', 'hello');

        $channel->close();
        $connection->close();

        return $this->json([
            'message' => ' [x] Sent Hello World!',
        ]);
    }

    #[Route('/job/message/receive', name: 'app_job_listen_message')]
    public function receive(): void
    {
        $connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare('hello', false, false, false, false);

        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        $callback = function ($msg) {
            sleep(3);
            echo ' [x] Received ', $msg->body, "\n";
        };

        $channel->basic_consume('hello', '', false, true, false, false, $callback);

        try {
            $channel->consume();
        } catch (\Throwable $exception) {
            echo $exception->getMessage();
        }
    }
}
