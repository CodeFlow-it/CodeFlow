<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Manage authentication via OAuth protocol to Github
 * 
 * @package App\Controller
 */
class GithubController extends AbstractController
{
    /**
     * Connect to the application via github OAuth
     *
     * @param  ClientRegistry $clientRegistry
     * @return RedirectResponse
     */
    #[Route('/connect/github', name: 'connect_github', methods: 'GET')]    
    public function connectAction(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry->getClient('github')->redirect(['user', 'user:email'], ['user']);
    }

    // ** if you want to *authenticate* the user, then
    // leave this method blank and create a Guard authenticator
    #[Route('/oauth/check/github', name: 'connect_github_check')]
    public function connectCheckAction(Request $request)
    {
    }
}
