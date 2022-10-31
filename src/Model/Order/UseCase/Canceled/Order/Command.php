<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Canceled\Order;

use App\Model\Order\Entity\Order;
use App\Model\Order\Entity\Status;

class Command
{
    public $order;
    public $status;

    public static function byOrder(Order $order): self
    {
        $command = new self();
        $command->order = $order->getId();
        $command->status = Status::CANCELED;
        return $command;
    }

    public static function fromClient(Order $order): self
    {
        $command = new self();
        $command->order = $order->getId();
        $command->status = Status::CANCELED_CLIENT;
        return $command;
    }
}
