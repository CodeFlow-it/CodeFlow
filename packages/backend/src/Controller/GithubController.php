<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GithubController extends AbstractController
{

    #[Route('/connect/github', name: 'connect_github')]
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry->getClient('github')->redirect(['user','user:email'], ['user']);
    }

    // ** if you want to *authenticate* the user, then
    // leave this method blank and create a Guard authenticator
    #[Route('/oauth/check/github', name: 'connect_github_check')]
    public function connectCheckAction(Request $request)
    {
    }
}
