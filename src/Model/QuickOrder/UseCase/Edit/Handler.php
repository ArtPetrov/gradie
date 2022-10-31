<?php

declare(strict_types=1);

namespace App\Model\QuickOrder\UseCase\Edit;

use App\Model\Flusher;
use App\Model\QuickOrder\Entity\Status;
use App\Model\QuickOrder\Repository\QuickOrderRepository;

class Handler
{
    private $flusher;
    private $orders;

    public function __construct(Flusher $flusher, QuickOrderRepository $orders)
    {
        $this->flusher = $flusher;
        $this->orders = $orders;
    }

    public function handle(Command $command): void
    {
        $order = $this->orders->get((int)$command->order);
        $order
            ->updateManagerComment($command->comment)
            ->changeStatus(new Status($command->status));
        $this->flusher->flush($order);
    }
}
