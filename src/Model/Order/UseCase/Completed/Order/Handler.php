<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Completed\Order;

use App\Model\Flusher;
use App\Model\Order\Entity\Invoice\Invoice;
use App\Model\Order\Entity\Status;
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
        $order = $this->orders->get((int)$command->order);

        if ($order->isCanceled() || $order->isCompleted()) {
            throw new \DomainException('order.error.canceled.completed');
        }

        $order->changeStatus(new Status(Status::COMPLETED));

        /** @var Invoice $invoice */
        foreach ($order->getInvoices() as $invoice) {
            $invoice->paid();
        }

        $this->flusher->flush($order);
    }
}
