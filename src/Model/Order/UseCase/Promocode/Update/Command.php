<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Promocode\Update;

use App\Model\Order\Entity\Order;

class Command
{
    public $order;
    public $promocode;

    public static function fromPromo(Order $order, ?string $promocode): self
    {
        $command = new self();
        $command->order = $order->getUuid();
        $command->promocode =  $promocode;
        return $command;
    }
}
