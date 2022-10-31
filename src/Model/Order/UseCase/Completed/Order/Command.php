<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Completed\Order;

use App\Model\Order\Entity\Order;

class Command
{
    public $order;

    public static function byOrder(Order $order): self
    {
        $command = new self();
        $command->order = $order->getId();
        return $command;
    }
}
