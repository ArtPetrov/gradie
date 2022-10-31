<?php

declare(strict_types=1);

namespace App\Controller\Buyer;

use App\Controller\ErrorHandler;
use App\Model\Buyer\UseCase\Network\Detach;
use App\Model\Buyer\UseCase\Network\Attach;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_BUYER")
 */
class OAuthController extends AbstractController
{
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/oauth/{network}/deatch", name="oauth.detach")
     */
    public function detach(string $network, Detach\Handler $handler): Response
    {
        if (!$network = $this->getUser()->findNetwork($network)) {
            $this->addFlash('error', 'buyer.network.not.found');
            return $this->redirectToRoute('buyer.settings');
        }
        $command = new Detach\Command($this->getUser()->getId(), $network->getNetwork(), $network->getIdentity());
        try {
            $handler->handle($command);
            $this->addFlash('success', 'buyer.network.deatach');
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('buyer.settings');
    }

    /**
     * @Route("/oauth/{network}/conect", name="oauth.connect")
     */
    public function connect(string $network, ClientRegistry $clientRegistry): Response
    {
        if (!in_array($network, $clientRegistry->getEnabledClientKeys())) {
            return $this->redirectToRoute('buyer.settings');
        }
        return $clientRegistry
            ->getClient($network)
            ->redirect([], []);
    }

    /**
     * @Route("/oauth/{network}/{client}/check", name="oauth.check")
     */
    public function check(string $network, string $client, Request $request, ClientRegistry $clientRegistry, Attach\Handler $handler): Response
    {
        if (!in_array($client, $clientRegistry->getEnabledClientKeys())) {
            return $this->redirectToRoute('buyer.settings');
        }

        if ($request->query->has('error')) {
            $this->addFlash('error', $request->query->get('error_description', 'Ошибка не указана'));
            return $this->redirectToRoute('buyer.settings');
        }

        $client = $clientRegistry->getClient($client);
        $command = new Attach\Command(
            $this->getUser()->getId(),
            $network,
            (string)$client->fetchUser()->getId()
        );

        try {
            $handler->handle($command);
            $this->addFlash('success', 'buyer.network.attach');
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('buyer.settings');
    }
}
