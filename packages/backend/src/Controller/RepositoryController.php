<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;

class RepositoryController extends AbstractController
{
    #[Route('api/repository/{name}', name: 'app_repository')]
    public function index(string $name): JsonResponse
    {
        $username = '';
        $accessToken = '';
        $targetDirectory = $this->createUserDirectory($username);

        try {

            $repository = $this->getGitHubRepositoryInformation($username, $accessToken);
            $repositoryUrl = $repository['html_url'];

            $this->cloneRepository($repositoryUrl, $targetDirectory);

            return $this->json([
                'message' => 'Repository cloned successfully',
                'repository_info' => $repository,
            ]);
        } catch (TransportException $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    private function createUserDirectory(string $username): string
    {
        $targetDirectory = $this->getParameter('kernel.project_dir') . '/repositories/' . $username;
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0777, true);
        }

        return $targetDirectory;
    }

    private function getGitHubRepositoryInformation(string $name, string $accessToken): array
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'https://api.github.com/repos/' . $name, [
            'headers' => [
                'Accept' => 'application/vnd.github+json',
                'Authorization' => 'Bearer ' . $accessToken,
                'X-GitHub-Api-Version' => '2022-11-28',
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Unable to retrieve repository information.');
        }

        return $response->toArray();
    }

    private function cloneRepository(string $url, string $targetDirectory): void
    {
        $process = new Process(['git', 'clone', $url, $targetDirectory]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}
