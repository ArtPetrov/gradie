<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Ordering\Payment;

use App\Model\Flusher;
use App\Model\Order\Entity\Order;
use App\Model\Order\Entity\Payment;
use App\Model\Order\Entity\Status;
use App\Model\Order\Event\CreateOrderEvent;
use App\Model\Order\Repository\OrderRepository;
use App\Model\Order\Service\CurrentOrder;
use App\Model\Order\Service\TransferProductsFromBasket;
use App\Model\Order\UseCase\Create\Invoices;

class Handler
{
    private $flusher;
    private $orders;
    private $transferProductsFromBasket;
    private $currentOrder;
    private $invoices;

    public function __construct(
        Flusher $flusher,
        OrderRepository $orders,
        TransferProductsFromBasket $transferProductsFromBasket,
        CurrentOrder $currentOrder,
        Invoices\Handler $invoices
    )
    {
        $this->flusher = $flusher;
        $this->orders = $orders;
        $this->transferProductsFromBasket = $transferProductsFromBasket;
        $this->currentOrder = $currentOrder;
        $this->invoices = $invoices;
    }

    public function handle(Command $command): Order
    {
        $order = $this->orders->getByUuid($command->order);
        $basketToken = $order->getBasket();
        $this->transferProductsFromBasket->inOrder($basketToken, $order);
        $payment = Payment::create($command->type);
        $order->updatePayment($payment);
        $order->changeStatus(new Status($payment->isOnline() ? Status::PENDING_PAYMENT : Status::IN_PROCESSING));
        $this->currentOrder->closedSession();
        $this->invoices->handle(Invoices\Command::fromOrder($order));
        $order->recordEvent(CreateOrderEvent::create($order));
        $this->flusher->flush($order);
        return $order;
    }
}
