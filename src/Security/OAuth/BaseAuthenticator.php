<?php

declare(strict_types=1);

namespace App\Security\OAuth;

use App\Model\Buyer\Repository\BuyerRepository;
use App\Model\Buyer\UseCase\Network\Auth\Command;
use App\Model\Buyer\UseCase\Network\Auth\Handler;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class BaseAuthenticator extends SocialAuthenticator
{
    protected $routerCheckName = 'oauth.check';
    protected $providerName = '';
    protected $networkName = '';
    private $urlGenerator;
    private $clients;
    private $handler;
    private $buyers;
    private $session;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        ClientRegistry $clients,
        Session $session,
        Handler $handler,
        BuyerRepository $buyers
    )
    {
        $this->urlGenerator = $urlGenerator;
        $this->clients = $clients;
        $this->handler = $handler;
        $this->buyers = $buyers;
        $this->session = $session;
    }

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === $this->routerCheckName &&
            $request->attributes->get('_route_params')['network'] === $this->networkName;
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getClient());
    }

    public function getCommand(ResourceOwnerInterface $user): Command
    {
        $command = new Command($this->networkName, (string)$user->getId());
        $command->name = implode(' ', [$user->getFirstName(), $user->getLastName()]);
        return $command;
    }

    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface
    {
        $user = $this->getClient()->fetchUserFromToken($credentials);
        try {
            return $this->buyers->getForAuthByNetwork($this->networkName, (string)$user->getId());
        } catch (\DomainException $e) {
            $this->handler->handle($this->getCommand($user));
            return $this->buyers->getForAuthByNetwork($this->networkName, (string)$user->getId());
        }
    }

    private function getClient(): OAuth2ClientInterface
    {
        return $this->clients->getClient($this->providerName);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        return new RedirectResponse($this->urlGenerator->generate('buyer.index'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());
        $this->session->getFlashBag()->add('error', $message);
        return new RedirectResponse($this->urlGenerator->generate('buyer.login'));
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->urlGenerator->generate('buyer.login'));
    }
}