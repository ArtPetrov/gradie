<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Promocode\Remove;

use App\Model\Order\Entity\Order;

class Command
{
    public $order;

    public static function fromPromo(Order $order): self
    {
        $command = new self();
        $command->order = $order->getUuid();
        return $command;
    }
}
