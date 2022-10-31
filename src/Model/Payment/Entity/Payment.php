<?php

declare(strict_types=1);

namespace App\Model\Payment\Entity;

use App\Model\EventBus;
use App\Model\EventBusTrait;
use App\Model\Order\Entity\Order;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity
 * @ORM\Table(name="payment")
 */
class Payment implements EventBus
{
    use EventBusTrait;
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="guid", nullable=false, unique=true)
     */
    private $uuid;

    /**
     * @var ArrayCollection|Invoice[]
     * @ORM\OneToMany(targetEntity="Invoice", mappedBy="payment", orphanRemoval=true, cascade={"persist"})
     */
    private $invoices;

    /**
     * @var Status
     * @ORM\Embedded(class="Status")
     */
    private $status;

    /**
     * @ORM\Column(type="decimal", scale=2, options={"default":0.00, "unsigned"=true})
     */
    private $sum;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $details;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    private function __construct()
    {
        $this->uuid = Uuid::uuid4()->toString();
        $this->invoices = new ArrayCollection();
    }

    public static function createForOrder(Order $order): self
    {
        $command = new self();
        $command->status = new Status(Status::WAIT);
        $command->sum = $order->getTotalWaitPay();
        foreach ($order->getInvoicesForPay() as $invoice) {
            $command->appendInvoice(Invoice::create($command, $invoice));
        }
        return $command;
    }

    public static function createForInvoice(\App\Model\Order\Entity\Invoice\Invoice $invoice): self
    {
        $command = new self();
        $command->status = new Status(Status::WAIT);
        $command->sum = $invoice->getSum();
        $command->appendInvoice(Invoice::create($command, $invoice));
        return $command;
    }

    public function getInvoices(): array
    {
        return $this->invoices->toArray();
    }

    public function getOrder(): Order
    {
        if ($this->invoices->count() === 0) {
            throw new \DomainException('payment.not.have.invoices');
        }
        /** @var Invoice $invoice */
        $invoice = $this->invoices->first();
        return $invoice->getInvoice()->getOrder();
    }

    public function appendInvoice(Invoice $invoice): self
    {
        $this->invoices->add($invoice);
        return $this;
    }

    public function getId(): int
    {
        return  (int)$this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getSum(): float
    {
        return (float)$this->sum;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function paid(): self
    {
        if ($this->status->isPaid()) {
            throw new \DomainException('payment.is.paid');
        }
        $this->status = new Status(Status::PAID);
        foreach ($this->invoices as $invoice) {
            $invoice->getInvoice()->paid();
        }
        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function updateDetails(?string $details): self
    {
        $this->details = $details;
        return $this;
    }
}
