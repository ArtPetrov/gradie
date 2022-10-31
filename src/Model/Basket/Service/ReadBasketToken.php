<?php

declare(strict_types=1);

namespace App\Model\Basket\Service;

use App\Helper\BasketTokenInterface;
use App\Model\Basket\Entity\BasketToken;
use App\Model\Basket\UseCase\Update\BuyerToken;
use App\Model\Buyer\Entity\Buyer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ReadBasketToken
{
    /** @var Buyer|null */
    private $user;
    private $buyerUpdateToken;
    /** @var Request|null */
    private $request;
    /**
     * @var ReadGuestToken
     */
    private $guestToken;

    public function __construct(
        BuyerToken\Handler $buyerUpdateToken,
        RequestStack $request,
        TokenStorageInterface $tokenStorage,
        ReadGuestToken $guestToken
    )
    {
        $this->buyerUpdateToken = $buyerUpdateToken;
        $this->request = $request->getCurrentRequest();
        $user = $tokenStorage->getToken()->getUser();
        if ($user instanceof Buyer) {
            $this->user = $user;
        }
        $this->guestToken = $guestToken;
    }

    public function getToken(): BasketTokenInterface
    {
        if ($this->user) {
            $token = $this->user->getBasketToken()->isAvailable() ? $this->user->getBasketToken() : $this->generateForUser($this->user);
            return new BasketToken($token->getToken());
        }
        return $this->guestToken->getToken();
    }

    public function generateForUser(Buyer $buyer): BasketTokenInterface
    {
        $token = (new \App\Model\Buyer\Service\BasketTokenizer())->generate();
        $command = new BuyerToken\Command($buyer, $token);
        $this->buyerUpdateToken->handle($command);
        return $token;
    }
}
