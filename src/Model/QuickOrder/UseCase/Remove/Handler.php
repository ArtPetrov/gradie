<?php

declare(strict_types=1);

namespace App\Model\QuickOrder\UseCase\Remove;

use App\Model\Flusher;
use App\Model\QuickOrder\Repository\QuickOrderRepository;

class Handler
{
    private $orders;
    private $flusher;

    public function __construct(QuickOrderRepository $orders, Flusher $flusher)
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
