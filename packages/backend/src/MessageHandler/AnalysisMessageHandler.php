<?php

namespace App\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Message\AnalysisMessage;
use App\Service\QueueManagerService;
use App\Service\PHPStanAnalysisService;

#[AsMessageHandler]
class AnalysisMessageHandler
{
    private QueueManagerService $queueManagerService;
    private PHPStanAnalysisService $PHPStanAnalysisService;

    public function __construct(QueueManagerService $queueManagerService, PHPStanAnalysisService $PHPStanAnalysisService)
    {
        $this->queueManagerService = $queueManagerService;
        $this->PHPStanAnalysisService = $PHPStanAnalysisService;
    }

    public function __invoke(AnalysisMessage $message)
    {
        $this->queueManagerService->createQueue($message->getType());        
        $this->queueManagerService->publishMessage($message->getType(), $message->getDirectory());
        $this->PHPStanAnalysisService->run($message->getDirectory(), $message->getProjectId());
    }
}