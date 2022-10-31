<?php

declare(strict_types=1);

namespace App\Model\Payment\Entity;

use App\Model\EventBus;
use App\Model\EventBusTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="payment_invoice")
 */
class Invoice implements EventBus
{
    use EventBusTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Payment
     * @ORM\ManyToOne(targetEntity="Payment", inversedBy="invoices")
     * @ORM\JoinColumn(name="payment", onDelete="CASCADE", nullable=false)
     */
    private $payment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Order\Entity\Invoice\Invoice")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE", name="invoice", nullable=false)
     */
    private $invoice;

    public static function create(Payment $payment, \App\Model\Order\Entity\Invoice\Invoice $invoice): self
    {
        $command = new self();
        $command->payment = $payment;
        $command->invoice = $invoice;
        return $command;
    }

    public function getId(): int
    {
        return (int)$this->id;
    }

    public function getInvoice(): \App\Model\Order\Entity\Invoice\Invoice
    {
        return $this->invoice;
    }
}
