<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Create\FromManager;

use App\Model\Flusher;
use App\Model\Order\Entity\Invoice\Invoice;
use App\Model\Order\Entity\Invoice\Type;
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
        $order = $this->orders->getByUuid($command->order);

        if($order->isCompleted())
        {
            throw new \DomainException('invoice.error.order.completed');
        }

        if($order->isCanceled())
        {
            throw new \DomainException('invoice.error.order.canceled');
        }

        if ((float)$command->sum <= 0) {
            throw new \DomainException('invoice.error.less.zero');
        }

        if($command->type!==Type::ADDITIONAL && $command->type!==Type::SHIPPING)
        {
            throw new \DomainException('invoice.error.type.not.supported');
        }

        $invoice = Invoice::create($order, new Type($command->type), (float)$command->sum, $command->comment);
        $order->createInvoice($invoice);
        $this->flusher->flush($order);
    }
}
