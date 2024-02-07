<?php

namespace App\Controller;

use App\Message\AnalysisMessage;
use App\Service\CloneRepositoryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class CodeAnalysisController
 *
 * @package App\Controller
 */
class CodeAnalysisController extends AbstractController
{
    /**
     * @var CloneRepositoryService
     */
    private CloneRepositoryService $cloneRepositoryService;

    /**
     * CodeAnalysisController constructor
     *
     * @param CloneRepositoryService $cloneRepositoryService
     */
    public function __construct(CloneRepositoryService $cloneRepositoryService)
    {
        $this->cloneRepositoryService = $cloneRepositoryService;
    }

    /**
     * Runs the analysis for a project
     *
     * @param Request $request The request object
     * @param string $projectName The name of the project
     * @param MessageBusInterface $bus The message bus interface
     * @return JsonResponse The JSON response
     */
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
            'message' => 'Analysis request executed'
        ]);
    }
}
