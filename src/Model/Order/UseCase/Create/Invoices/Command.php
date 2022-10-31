<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Create\Invoices;

use App\Model\Order\Entity\Order;

class Command
{
    public $order = null;

    public static function fromOrder(Order $order): self
    {
        $command = new self();
        $command->order = $order->getUuid();
        return $command;
    }
}
