<?php

namespace App\Controller;

use Symfony\Component\Process\Process;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RepositoryController extends AbstractController
{
    private const GITHUB_API_VERSION = '2022-11-28';

    #[Route('api/repositories', name: 'app_repositories', methods: 'GET')]
    public function index()
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $accessToken = $user->getAccessToken();
        $url = 'https://api.github.com/user/repos';

        $repositories = $this->fetchGithubApi($url, $accessToken);

        return $this->json(['repo' => $repositories]);
    }

    // #[Route('api/repository/clone/{projectName}', name: 'app_clone_repository')]
    // public function clone(string $projectName): JsonResponse
    // {
    //     /** @var \App\Entity\User $user */
    //     $user = $this->getUser();

    //     if (!$user) {
    //         return $this->json(['error' => 'Unauthorized'], 401);
    //     }

    //     $username = $user->getUsername();
    //     $accessToken = $user->getAccessToken();
    //     $targetDirectory = $this->createUserDirectory($username, $projectName);

    //     try {
    //         $repository = $this->getGitHubRepositoryInformation($username, $projectName, $accessToken);
    //         $repositoryUrl = $repository['html_url'];

    //         $this->cloneRepository($repositoryUrl, $targetDirectory);

    //         return $this->json([
    //             'message' => 'Repository cloned successfully',
    //             'repository_info' => $repository,
    //         ]);
    //     } catch (TransportException $e) {
    //         return $this->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    private function getGitHubRepositoryInformation(string $username, string $name, string $accessToken): array
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'https://api.github.com/repos/' . $username . '/' . $name, [
            'headers' => [
                'Accept' => 'application/vnd.github+json',
                'Authorization' => 'Bearer ' . $accessToken,
                'X-GitHub-Api-Version' => self::GITHUB_API_VERSION,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Unable to retrieve repository information.');
        }

        return $response->toArray();
    }

    private function fetchGithubApi(string $url, string $accessToken): array
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', $url, [
            'headers' => [
                'Accept' => 'application/vnd.github+json',
                'Authorization' => 'Bearer ' . $accessToken,
                'X-GitHub-Api-Version' => self::GITHUB_API_VERSION,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Unable to retrieve fetch api.');
        }

        return $response->toArray();
    }
}
