<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Ordering\Payment;

use App\Model\Order\Entity\Order;
use App\Model\Order\Entity\Payment;

class Command
{
    public $type = null;
    public $order = null;
    public $delivery = null;

    public static function fromOrder(Order $order): self
    {
        $command = new self();
        $command->order = $order->getUuid();
        $command->type = $order->getPayment()->getType() ?? Payment::ONLINE;
        $command->delivery = $order->getAddress()->getType();
        return $command;
    }
}
