<?php

declare(strict_types=1);

namespace App\Controller\OAuth;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class OAuthController extends AbstractController
{
    /**
     * @Route("/oauth/{network}", name="oauth.connect")
     */
    public function connect(string $network, ClientRegistry $clientRegistry): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')
            || $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')
        ) {
            return $this->redirectToRoute('buyer.index');
        };

        if (!in_array($network, $clientRegistry->getEnabledClientKeys())) {
            return $this->redirectToRoute('error.404');
        }

        return $clientRegistry
            ->getClient($network)
            ->redirect([], []);
    }

    /**
     * @Route("/oauth/{network}/check", name="oauth.check")
     */
    public function check(string $network): Response
    {
        return $this->redirectToRoute('catalog');
    }
}

