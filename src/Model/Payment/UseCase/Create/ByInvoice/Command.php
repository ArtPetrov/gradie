<?php

declare(strict_types=1);

namespace App\Model\Payment\UseCase\Create\ByInvoice;

use App\Model\Order\Entity\Invoice\Invoice;

class Command
{
    public $invoice;

    public static function fromInvoice(Invoice $invoice): self
    {
        $command = new self();
        $command->invoice = $invoice->getId();
        return $command;
    }
}
