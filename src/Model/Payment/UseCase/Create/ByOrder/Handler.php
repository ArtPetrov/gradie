<?php

declare(strict_types=1);

namespace App\Model\Payment\UseCase\Create\ByOrder;

use App\Model\Flusher;
use App\Model\Order\Repository\OrderRepository;
use App\Model\Payment\Entity\Payment;
use App\Model\Payment\Repository\PaymentRepository;

class Handler
{
    private $flusher;
    private $orders;
    private $payments;

    public function __construct(Flusher $flusher, OrderRepository $orders, PaymentRepository $payments)
    {
        $this->flusher = $flusher;
        $this->orders = $orders;
        $this->payments = $payments;
    }

    public function handle(Command $command): Payment
    {
        $order = $this->orders->getByUuid($command->order);

        if (count($order->getInvoicesForPay())===0 ) {
            throw new \DomainException('payment.order.incorrectly');
        }
        $payment = Payment::createForOrder($order);
        $this->payments->add($payment);
        $this->flusher->flush($payment);
        return $payment;
    }
}
