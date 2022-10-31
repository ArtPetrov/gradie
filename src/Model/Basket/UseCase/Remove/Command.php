<?php

declare(strict_types=1);

namespace App\Model\Basket\UseCase\Remove;

use App\Helper\BasketTokenInterface;

class Command
{
    /** @var string|null */
    public $token;
    /** @var int */
    public $product;

    public function __construct(BasketTokenInterface $token, int $product)
    {
        $this->token = $token->getToken();
        $this->product = $product;
    }
}
