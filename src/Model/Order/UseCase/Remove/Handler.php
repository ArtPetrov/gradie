<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Remove;

use App\Model\Flusher;
use App\Model\Order\Repository\OrderRepository;

class Handler
{
    private $orders;
    private $flusher;

    public function __construct(OrderRepository $orders, Flusher $flusher)
    {
        $this->orders = $orders;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $order = $this->orders->get($command->id);
        $this->orders->remove($order);
        $this->flusher->flush();
    }
}
