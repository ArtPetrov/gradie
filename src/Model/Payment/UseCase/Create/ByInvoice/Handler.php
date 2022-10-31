<?php

declare(strict_types=1);

namespace App\Model\Payment\UseCase\Create\ByInvoice;

use App\Model\Flusher;
use App\Model\Order\Repository\InvoiceRepository;
use App\Model\Payment\Entity\Payment;
use App\Model\Payment\Repository\PaymentRepository;

class Handler
{
    private $flusher;
    private $invoices;
    private $payments;

    public function __construct(Flusher $flusher, InvoiceRepository $invoices, PaymentRepository $payments)
    {
        $this->flusher = $flusher;
        $this->invoices = $invoices;
        $this->payments = $payments;
    }

    public function handle(Command $command): Payment
    {
        $invoice = $this->invoices->get($command->invoice);

        if (!$invoice->isWaitPay()) {
            throw new \DomainException('payment.invoice.incorrectly');
        }

        $payment = Payment::createForInvoice($invoice);
        $this->payments->add($payment);
        $this->flusher->flush($payment);
        return $payment;
    }
}
