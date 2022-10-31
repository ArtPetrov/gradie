<?php

declare(strict_types=1);

namespace App\Model\Basket\Service;

use App\Helper\BasketTokenInterface;
use App\Model\Basket\Entity\BasketToken;
use Symfony\Component\HttpFoundation\RequestStack;

class ReadGuestToken
{
    private $guestToken;

    public function __construct(RequestStack $request)
    {
        $guestToken = $request->getCurrentRequest()->cookies->get(BasketToken::COOKIE_NAME);
        if ($guestToken) {
            $this->guestToken = new BasketToken($guestToken);
        }
    }

    public function getToken(): ?BasketTokenInterface
    {
        return $this->guestToken;
    }
}
