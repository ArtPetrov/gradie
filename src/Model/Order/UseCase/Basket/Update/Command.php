<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Basket\Update;

use App\Model\Order\Entity\Order;

class Command
{
    public $basket;
    public $product;
    public $count;

    public static function fromOrder(Order $order, int $product, int $count): self
    {
        $command = new self();
        $command->basket = $order->getBasket();
        $command->product = $product;
        $command->count = $count;
        return $command;
    }
}
