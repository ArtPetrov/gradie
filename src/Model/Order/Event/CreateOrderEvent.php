<?php

declare(strict_types=1);

namespace App\Model\Order\Event;

use App\Model\Order\Entity\Order;
use Symfony\Contracts\EventDispatcher\Event;

class CreateOrderEvent extends Event
{
    private $id;

    public static function create(Order $order): self
    {
        $event = new self();
        $event->id = $order->getId();
        return $event;
    }

    public function getIdOrder(): int
    {
        return $this->id;
    }
}
