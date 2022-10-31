<?php

declare(strict_types=1);

namespace App\Model\Payment\UseCase\Create;

use App\Model\Payment\Entity\Payment;

class Command
{
    public $sum;
    public $id;

    public static function fromPayment(Payment $payment): self
    {
        $command = new self();
        $command->id = $payment->getId();
        $command->sum = $payment->getSum();
        return $command;
    }
}
