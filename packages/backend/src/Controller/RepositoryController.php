<?php

namespace App\Controller;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller managing operations related to repositories
 * 
 * @package App\Controller
 */
class RepositoryController extends AbstractController
{
    private const GITHUB_API_VERSION = '2022-11-28';

    /**
     * Retrieves the list of repositories for the authenticated user from GitHub API
     *
     * @return JsonResponse The JSON response containing the list of repositories
     */
    #[Route('api/repositories', name: 'app_repositories', methods: 'GET')]
    public function index(): JsonResponse
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

    /**
     * Retrieves data from the GitHub API
     *
     * @param string $url The URL of the API to query
     * @param string $accessToken The access token for authentication
     * @return array Data retrieved from the API
     */
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
