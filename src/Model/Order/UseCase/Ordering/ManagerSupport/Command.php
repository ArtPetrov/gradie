<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Ordering\ManagerSupport;

use App\Model\Order\Entity\Order;

class Command
{
    public $help = null;
    public $order = null;

    public static function fromOrder(Order $order): self
    {
        $command = new self();
        $command->order = $order->getUuid();
        $command->help = $order->isHelpNeed() ? 'need' : 'cancel';
        return $command;
    }
}
