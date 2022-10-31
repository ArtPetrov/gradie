<?php

declare(strict_types=1);

namespace App\Model\QuickOrder\UseCase\Edit;

use App\Model\QuickOrder\Entity\Order;

class Command
{
    public $order;
    public $status;
    public $comment;

    public static function fromAdministrator(Order $order): self
    {
        $command = new self();
        $command->order = $order->getId();
        $command->status = $order->getStatus()->getStatus();
        $command->comment = $order->getManagerComment();
        return $command;
    }
}
