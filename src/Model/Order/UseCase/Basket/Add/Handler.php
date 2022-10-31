<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Basket\Add;

use App\Model\Basket\Entity\BasketToken;
use App\Model\Basket\Entity\Item;
use App\Model\Basket\Repository\BasketRepository;
use App\Model\Ecommerce\Repository\ProductRepository;
use App\Model\Order\UseCase\Canceled;
use App\Model\Flusher;


class Handler
{
    private $flusher;
    private $basket;
    private $products;

    public function __construct(Flusher $flusher, BasketRepository $basket, ProductRepository $products)
    {
        $this->flusher = $flusher;
        $this->basket = $basket;
        $this->products = $products;
    }

    public function handle(Command $command): void
    {
        $product = $this->products->get((int)$command->product);
        $token = new BasketToken($command->basket);

        if ($this->basket->findByTokenAndProduct($token, $product->getId())) {
            throw new \DomainException('product.exist.in.basket');
        }

        $item = Item::create($token, $product, 1);
        $this->basket->add($item);
        $this->flusher->flush($item);
    }
}
