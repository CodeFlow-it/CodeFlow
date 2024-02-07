<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Project;
use App\Message\AnalysisMessage;
use App\Service\CloneRepositoryService;
use Doctrine\ORM\EntityManagerInterface;
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
    private EntityManagerInterface $entityManager;

    /**
     * CodeAnalysisController constructor
     *
     * @param CloneRepositoryService $cloneRepositoryService
     */
    public function __construct(CloneRepositoryService $cloneRepositoryService, EntityManagerInterface $entityManager)
    {
        $this->cloneRepositoryService = $cloneRepositoryService;
        $this->entityManager = $entityManager;
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

        $parameters = json_decode($request->getContent());
        $analysis = $parameters->analysis;
        $repositoryUrl = $parameters->repoUrl;

        $projectId = $this->setProject($user, $repositoryUrl);

        $targetDirectory = $this->cloneRepositoryService->clone($repositoryUrl, (string)$user->getId(), (string)$projectId);
        $this->setProjectSource($projectId, $targetDirectory);

        $bus->dispatch(new AnalysisMessage($targetDirectory, $analysis, $projectId, $user->getId()));

        return $this->json([
            'message' => 'Analysis request executed',
        ]);
    }

    /**
     * Create or update a project
     *
     * @param  User $user
     * @param  string $repositoryUrl
     * @param  string $targetDirectory
     * @return int The project id
     */
    private function setProject(User $user, string $repositoryUrl): int
    {
        $project = $this->entityManager->getRepository(Project::class)->findOneBy(['url' => $repositoryUrl]);

        if (!$project) {
            $project = new Project();
            $project->setUser($user);
            $project->setUrl($repositoryUrl);
            $project->setCreatedAt(new DateTime('now'));
        } else {
            $project->setUpdatedAt(new DateTime('now'));
        }

        $this->entityManager->persist($project);
        $this->entityManager->flush();

        return $project->getId();
    }

    /**
     * Set the project source
     *
     * @param  string $id
     * @param  string $source
     * @return void
     */
    private function setProjectSource(int $id, string $source)
    {
        $project = $this->entityManager->getRepository(Project::class)->findOneBy(['id' => $id]);
        $project->setSource($source);

        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }
}
