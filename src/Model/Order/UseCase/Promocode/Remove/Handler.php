<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Promocode\Remove;

use App\Model\Flusher;
use App\Model\Order\Entity\Promocode;
use App\Model\Order\Repository\OrderRepository;

class Handler
{
    private $flusher;
    private $orders;

    public function __construct(Flusher $flusher, OrderRepository $orders)
    {
        $this->flusher = $flusher;
        $this->orders = $orders;
    }

    public function handle(Command $command): void
    {
        if (!$order = $this->orders->findByUuid($command->order)) {
            return;
        }
        $order->updatePromocode(new Promocode());
        $this->flusher->flush($order);
    }
}
