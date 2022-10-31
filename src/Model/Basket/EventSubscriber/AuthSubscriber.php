<?php

declare(strict_types=1);

namespace App\Model\Basket\EventSubscriber;

use App\Model\Basket\Service\BasketSync;
use App\Model\Basket\Service\ReadGuestToken;
use App\Model\Basket\UseCase\Update\BuyerToken\Command;
use App\Model\Basket\UseCase\Update\BuyerToken\Handler;
use App\Model\Buyer\Service\BasketTokenizer;
use App\Model\Buyer\Entity\Buyer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class AuthSubscriber implements EventSubscriberInterface
{
    private $guestToken = null;
    private $tokenizer;
    private $buyerToken;
    private $basketSync;

    public function __construct(
        BasketTokenizer $tokenizer,
        Handler $buyerToken,
        BasketSync $basketSync,
        ReadGuestToken $guestToken
    )
    {
        $this->guestToken = $guestToken->getToken();
        $this->tokenizer = $tokenizer;
        $this->buyerToken = $buyerToken;
        $this->basketSync = $basketSync;
    }

    public function diffBasket(AuthenticationEvent $event)
    {
        $token = $event->getAuthenticationToken();
        if (!$token instanceof PostAuthenticationGuardToken) {
            return;
        }

        $user = $token->getUser();
        if (!$user instanceof Buyer) {
            return;
        }

        if (!$this->guestToken) {
            return;
        }

        $basketToken = $user->getBasketToken();
        if (!$basketToken->isAvailable()) {
            $basketToken = $this->tokenizer->generate();
            $this->buyerToken->handle(new Command($user, $basketToken));
        }

        $this->basketSync->searchDifficult($this->guestToken, $basketToken);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AuthenticationEvents::AUTHENTICATION_SUCCESS => 'diffBasket',
        ];
    }
}
