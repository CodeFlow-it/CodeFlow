<?php

namespace App\Controller;

use App\Message\AnalysisMessage;
use App\Service\CloneRepositoryService;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CodeAnalysisController extends AbstractController
{
    private CloneRepositoryService $cloneRepositoryService;

    public function __construct(CloneRepositoryService $cloneRepositoryService)
    {
        $this->cloneRepositoryService = $cloneRepositoryService;
    }

    #[Route('/api/analysis/{projectName}', name: 'app_run_analysis', methods: 'POST')]
    public function run(Request $request, string $projectName, MessageBusInterface $bus): JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $username = $user->getUsername();
        $parameters = json_decode($request->getContent());
        $analysis = $parameters->analysis;
        $repositoryUrl = $parameters->repoUrl;

        $targetDirectory = $this->cloneRepositoryService->clone($repositoryUrl, $username, $projectName);

        $bus->dispatch(new AnalysisMessage($targetDirectory, $analysis));

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/CodeAnalysisController.php',
        ]);
    }
}
