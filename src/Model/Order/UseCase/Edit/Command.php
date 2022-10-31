<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Edit;

use App\Model\Order\Entity\Order;

class Command
{
    public $order = null;
    public $comment;
    public $status;
    public $currentStatus;

    public static function fromOrder(Order $order): self
    {
        $command = new self();
        $command->order = $order->getUuid();
        $command->comment = $order->getManagerComment();
        $command->status = $order->getStatus()->availableForManager() ? $order->getStatus()->getValue() : null;
        $command->currentStatus = $order->getStatus()->getName();
        return $command;
    }
}
