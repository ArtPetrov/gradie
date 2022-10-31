<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Edit;

use App\Model\Order\UseCase\Canceled;
use App\Model\Order\UseCase\Completed\Order;
use App\Model\Flusher;
use App\Model\Order\Entity\Status;
use App\Model\Order\Repository\OrderRepository;

class Handler
{
    private $flusher;
    private $orders;
    private $canceledHandler;
    private $completedHandler;

    public function __construct(Flusher $flusher, OrderRepository $orders, Canceled\Order\Handler $canceledHandler, Order\Handler $completedHandler)
    {
        $this->flusher = $flusher;
        $this->orders = $orders;
        $this->canceledHandler = $canceledHandler;
        $this->completedHandler = $completedHandler;
    }

    public function handle(Command $command): void
    {
        $order = $this->orders->getByUuid($command->order);
        $order->editManagerComment($command->comment);

        if ($command->status && !$order->isCompleted() && !$order->isCanceled()) {
            if ($command->status == Status::CANCELED) {
                $this->canceledHandler->handle(Canceled\Order\Command::byOrder($order));
            } elseif ($command->status == Status::COMPLETED) {
                $this->completedHandler->handle(Order\Command::byOrder($order));
            } else {
                $order->changeStatus(new Status($command->status));
            }
        }

        $this->flusher->flush($order);
    }
}
