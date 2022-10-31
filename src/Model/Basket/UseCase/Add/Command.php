<?php

declare(strict_types=1);

namespace App\Model\Basket\UseCase\Add;

use App\Helper\BasketTokenInterface;

class Command
{
    public $token;
    public $product;
    public $count;

    public function __construct(BasketTokenInterface $token, int $product, int $count = 1)
    {
        $this->token = $token->getToken();
        $this->product = $product;
        $this->count = $count;
    }
}
