<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Canceled\Invoice;

use App\Model\Flusher;
use App\Model\Order\Repository\InvoiceRepository;

class Handler
{
    private $flusher;
    private $invoices;

    public function __construct(Flusher $flusher, InvoiceRepository $invoices)
    {
        $this->flusher = $flusher;
        $this->invoices = $invoices;
    }

    public function handle(Command $command): void
    {
        $invoice = $this->invoices->get($command->invoice);
        $invoice->canceled();
        $this->flusher->flush($invoice);
    }
}
