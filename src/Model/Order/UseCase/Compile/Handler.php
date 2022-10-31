<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Compile;

use App\Model\Flusher;
use App\Model\Order\Event\CreateOrderEvent;
use App\Model\Order\UseCase\Create\Invoices;
use App\Model\Order\Entity\Status;
use App\Model\Order\Repository\OrderRepository;
use App\Model\Order\Service\TransferProductsFromBasket;

class Handler
{
    private $flusher;
    private $orders;
    private $transferProductsFromBasket;
    private $invoices;

    public function __construct(
        Flusher $flusher,
        OrderRepository $orders,
        TransferProductsFromBasket $transferProductsFromBasket,
        Invoices\Handler $invoices
    )
    {
        $this->flusher = $flusher;
        $this->orders = $orders;
        $this->transferProductsFromBasket = $transferProductsFromBasket;
        $this->invoices = $invoices;
    }

    public function handle(Command $command): void
    {
        $order = $this->orders->get((int)$command->order);

        if (!$order->getStatus()->inProcessCreating()) {
            throw new \DomainException('order.not.process.creating');
        }

        $order->changeStatus(new Status(Status::IN_WORK));
        $this->transferProductsFromBasket->inOrder($order->getBasket(), $order);
        $this->invoices->handle(Invoices\Command::fromOrder($order));

        $order->recordEvent(CreateOrderEvent::create($order));

        $this->flusher->flush($order);
    }
}
