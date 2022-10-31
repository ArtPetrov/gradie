<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Basket\Remove;

use App\Model\Order\Entity\Order;

class Command
{
    public $basket;
    public $product;

    public static function fromOrder(Order $order, int $product): self
    {
        $command = new self();
        $command->basket = $order->getBasket();
        $command->product = $product;
        return $command;
    }
}
