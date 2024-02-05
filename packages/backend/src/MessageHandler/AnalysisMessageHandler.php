<?php

namespace App\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Message\AnalysisMessage;
use App\Service\QueueManagerService;

#[AsMessageHandler]
class AnalysisMessageHandler
{
    private QueueManagerService $queueManagerService;

    public function __construct(QueueManagerService $queueManagerService)
    {
        $this->queueManagerService = $queueManagerService;
    }

    public function __invoke(AnalysisMessage $message)
    {
        // On exécute un job d'analyse
        $this->queueManagerService->createQueue($message->getType());        
        $this->queueManagerService->publishMessage($message->getType(), $message->getDirectory());

        // Peut prendre du temps

        sleep(10);
    }
}