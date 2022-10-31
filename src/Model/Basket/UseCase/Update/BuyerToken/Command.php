<?php

declare(strict_types=1);

namespace App\Model\Basket\UseCase\Update\BuyerToken;

use App\Model\Buyer\Entity\BasketToken;
use App\Model\Buyer\Entity\Buyer;

class Command
{
    public $buyer;
    public $token;

    public function __construct(Buyer $buyer, ?BasketToken $token)
    {
        $this->token = $token->getToken();
        $this->buyer = $buyer->getId();
    }
}
