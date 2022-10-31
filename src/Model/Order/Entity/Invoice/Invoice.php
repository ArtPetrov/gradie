<?php

declare(strict_types=1);

namespace App\Model\Order\Entity\Invoice;

use App\Model\EventBus;
use App\Model\EventBusTrait;
use App\Model\Order\Entity\Order;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity
 * @ORM\Table(name="order_invoice",
 * indexes={
 *     @ORM\Index(name="order_invoice_order_idx", columns={"order_id"})
 * })
 */
class Invoice implements EventBus
{
    use EventBusTrait;
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Order\Entity\Order", inversedBy="invoices")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @var Type
     * @ORM\Embedded(class="Type")
     */
    private $type;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    private function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
    }

    public static function create(Order $order, Type $type, float $sum = 0.00, ?string $comment = ''): self
    {
        $command = new self();
        $command->order = $order;
        $command->status = new Status(Status::CREATE);
        $command->type = $type;
        $command->sum = $sum;
        $command->comment = $comment;
        return $command;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getOrder(): Order
    {
        return $this->order;
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
        $this->status = new Status(Status::PAID);
        return $this;
    }

    public function canceled(): self
    {
        if ($this->status == Status::PAID) {
            return $this;
        }
        $this->status = new Status(Status::CANCEL);
        return $this;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function isShipping(): bool
    {
        return $this->type->getValue() == Type::SHIPPING;
    }

    public function isCanceled(): bool
    {
        return $this->status->getValue() === Status::CANCEL;
    }

    public function isWaitPay(): bool
    {
        return $this->status->getValue() === Status::CREATE;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }
}
