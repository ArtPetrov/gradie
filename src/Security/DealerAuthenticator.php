<?php

declare(strict_types=1);

namespace App\Security;

use App\Model\Dealer\Repository\DealerRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class DealerAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    /**
     * @var DealerRepository
     */
    private $dealerRepository;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfToken;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(
        DealerRepository $dealerRepository,
        RouterInterface $router,
        CsrfTokenManagerInterface $csrfToken,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        $this->dealerRepository = $dealerRepository;
        $this->router = $router;
        $this->csrfToken = $csrfToken;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'dealer.login'
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = $request->request->get('form');
        $request->getSession()->set(Security::LAST_USERNAME, $credentials['email']);
        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface
    {
        if (!$this->csrfToken->isTokenValid(new CsrfToken('auth', $credentials['_token']))) {
            throw new InvalidCsrfTokenException();
        }
        $dealer = $this->dealerRepository->findOneBy(['email' => $credentials['email']]);
        if (!$dealer) {
            throw new CustomUserMessageAuthenticationException('Username could not be found.');
        }

        if ($dealer->moderation()->isWait()) {
            throw new CustomUserMessageAuthenticationException('dealer.moderation.wait');
        }

        return $dealer;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('dealer.index'));
    }

    protected function getLoginUrl(): string
    {
        return $this->router->generate('dealer.login');
    }
}
