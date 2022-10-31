<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Canceled\Invoice;

use App\Model\Order\Entity\Invoice\Invoice;

class Command
{
    public $invoice;

    public static function byInvoice(Invoice $invoice): self
    {
        $command = new self();
        $command->invoice = $invoice->getId();
        return $command;
    }
}
