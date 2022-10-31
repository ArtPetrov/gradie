<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Basket\Remove;

use App\Model\Basket\Repository\BasketRepository;
use App\Model\Ecommerce\Repository\ProductRepository;
use App\Model\Order\UseCase\Canceled;

class Handler
{
    private $basket;
    private $products;

    public function __construct(BasketRepository $basket, ProductRepository $products)
    {
        $this->basket = $basket;
        $this->products = $products;
    }

    public function handle(Command $command): void
    {
        $product = $this->products->get((int)$command->product);
        $this->basket->removeByTokenAndProduct($command->basket, $product);
    }
}
