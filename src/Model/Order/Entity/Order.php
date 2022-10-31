<?php

declare(strict_types=1);

namespace App\Model\Order\Entity;

use App\Helper\BasketTokenInterface;
use App\Model\EventBus;
use App\Model\EventBusTrait;
use App\Model\Order\Entity\Invoice\Invoice;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity()
 * @ORM\Table(name="orders",
 * indexes={
 *     @ORM\Index(name="uuid_idx", columns={"uuid"}),
 *     @ORM\Index(name="basket_token_idx", columns={"basket_token"})
 * })
 */
class Order implements EventBus
{
    use EventBusTrait;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\SequenceGenerator(initialValue=1000, sequenceName="order_seq")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="guid")
     */
    private $uuid;

    /**
     * @var BasketToken
     * @ORM\Embedded(class="BasketToken")
     */
    private $basket;

    /**
     * @ORM\Embedded(class="Status", columnPrefix=false)
     */
    private $status;

    /**
     * @ORM\Embedded(class="Contact", columnPrefix="client_")
     */
    private $contact;

    /**
     * @ORM\Embedded(class="Address", columnPrefix="address_")
     */
    private $address;

    /**
     * @ORM\Embedded(class="Payment", columnPrefix="payment_")
     */
    private $payment;

    /**
     * @var Promocode
     * @ORM\Embedded(class="Promocode", columnPrefix="promo_")
     */
    private $promocode;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $managerHelp;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $managerComment;

    /**
     * @var ArrayCollection|Product[]
     * @ORM\OneToMany(targetEntity="Product", mappedBy="order", orphanRemoval=true, cascade={"persist"})
     */
    private $products;

    /**
     * @var ArrayCollection|Invoice[]
     * @ORM\OneToMany(targetEntity="App\Model\Order\Entity\Invoice\Invoice", mappedBy="order", orphanRemoval=true, cascade={"persist"})
     */
    private $invoices;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    public function __construct()
    {
        $this->promocode = new Promocode();
        $this->payment = new Payment();
        $this->products = new ArrayCollection();
        $this->invoices = new ArrayCollection();
    }

    public static function create(Contact $contact, BasketTokenInterface $basket): self
    {
        $order = new self();
        $order->uuid = Uuid::uuid4()->toString();
        $order->basket = new BasketToken($basket->getToken());
        $order->changeStatus(new Status(Status::CLIENT_ENTERED_CONTACT));
        $order->updateContact($contact);
        return $order;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getContact(): Contact
    {
        return $this->contact;
    }

    public function getBasket(): BasketTokenInterface
    {
        return $this->basket;
    }

    public function updateContact(Contact $contact): self
    {
        $this->contact = $contact;
        return $this;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function changeAddress(Address $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function changeStatus(Status $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function isHelpNeed(): ?bool
    {
        return $this->managerHelp;
    }

    public function managerNeed(): self
    {
        $this->managerHelp = true;
        return $this;
    }

    public function managerCancel(): self
    {
        $this->managerHelp = false;
        return $this;
    }

    public function updatePromocode(Promocode $promo): self
    {
        $this->promocode = $promo;
        return $this;
    }

    public function hasPromocode(): bool
    {
        return (bool)$this->promocode->getCode();
    }

    public function getPromocode(): Promocode
    {
        return $this->promocode;
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }

    public function updatePayment(Payment $payment): self
    {
        $this->payment = $payment;
        return $this;
    }

    public function addProduct(\App\Model\Ecommerce\Entity\Product\Product $product, int $count = 1): self
    {
        $this->products->add(Product::create($this, $product, $count));
        return $this;
    }

    public function getProducts(): array
    {
        return $this->products->toArray();
    }

    public function getInvoices(): array
    {
        return $this->invoices->toArray();
    }

    public function getInvoicesForPay(): array
    {
        return $this->invoices->filter(static function (Invoice $invoice) {
            return $invoice->isWaitPay();
        })->toArray();
    }

    public function createInvoice(Invoice $invoice): self
    {
        $this->invoices->add($invoice);
        return $this;
    }

    public function editManagerComment(?string $comment): self
    {
        $this->managerComment = $comment;
        return $this;
    }

    public function getManagerComment(): ?string
    {
        return $this->managerComment;
    }

    public function getTotalPriceProducts(): float
    {
        $total = 0.00;
        /** @var Product $product */
        foreach ($this->getProducts() as $product) {
            $total += $product->getPrice() * $product->getCount();
        }
        return $total;
    }

    public function getTotalProducts(): int
    {
        $total = 0;
        /** @var Product $product */
        foreach ($this->getProducts() as $product) {
            $total += $product->getCount();
        }
        return $total;
    }

    public function getTotalPrice(): float
    {
        $total = 0.00;
        /** @var Invoice $invoice */
        foreach ($this->getInvoices() as $invoice) {
            if($invoice->isCanceled()){
                continue;
            }
            $total += $invoice->getSum();
        }
        return $total;
    }

    public function getShippingPrice(): float
    {
        $total = 0.00;
        /** @var Invoice $invoice */
        foreach ($this->getInvoices() as $invoice) {
            if($invoice->isShipping() && !$invoice->isCanceled()){
                $total += $invoice->getSum();
            }

        }
        return $total;
    }

    public function getTotalWaitPay(): float
    {
        $total = 0.00;
        /** @var Invoice $invoice */
        foreach ($this->getInvoicesForPay() as $invoice) {
            $total += $invoice->getSum();
        }
        return $total;
    }

    public function isCompleted(): bool
    {
        return $this->getStatus()->isCompleted();
    }

    public function isCanceled(): bool
    {
        return $this->getStatus()->isCanceled();
    }

    public function inProcessing(): bool
    {
        return $this->getStatus()->inProcessing();
    }
}
