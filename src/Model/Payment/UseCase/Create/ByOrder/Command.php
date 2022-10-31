<?php

declare(strict_types=1);

namespace App\Model\Payment\UseCase\Create\ByOrder;

use App\Model\Order\Entity\Order;

class Command
{
    public $order;

    public static function fromOrder(Order $order): self
    {
        $command = new self();
        $command->order = $order->getUuid();
        return $command;
    }
}
