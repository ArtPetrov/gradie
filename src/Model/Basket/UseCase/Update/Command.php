<?php

declare(strict_types=1);

namespace App\Model\Basket\UseCase\Update;

use App\Helper\BasketTokenInterface;

class Command
{
    /** @var string|null */
    public $token;
    /** @var int */
    public $product;
    /** @var int */
    public $count;

    public function __construct(BasketTokenInterface $token, int $product, int $count = 1)
    {
        $this->token = $token->getToken();
        $this->product = $product;
        $this->count = $count;
    }
}
