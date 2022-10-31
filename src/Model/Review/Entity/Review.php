<?php

declare(strict_types=1);

namespace App\Model\Review\Entity;

use App\Model\Buyer\Entity\Buyer;
use App\Model\Ecommerce\Entity\Product\Product;
use App\Model\EventBus;
use App\Model\EventBusTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity()
 */
class Review implements EventBus
{
    use EventBusTrait;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Product
     * @ORM\ManyToOne(targetEntity="App\Model\Ecommerce\Entity\Product\Product", inversedBy="reviews")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=false)
     */
    private $product;

    /**
     * @var Buyer|null
     * @ORM\ManyToOne(targetEntity="App\Model\Buyer\Entity\Buyer", inversedBy="reviews")
     * @ORM\JoinColumn(name="buyer_id", referencedColumnName="id")
     */
    private $buyer;

    /**
     * @var Status
     * @ORM\Embedded(class="Status", columnPrefix="moderation_")
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="smallint",  nullable=false, options={"unsigned":true, "default":5})
     */
    private $rating;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $message;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    private function __construct(Status $status)
    {
        $this->updateStatus($status);
    }

    public static function leftByAdministrator(Product $product, string $name, string $message, int $rating, \DateTime $date): self
    {
        $review = new self(new Status(Status::STATUS_ACTIVE));
        $review
            ->attachProduct($product)
            ->updateName($name)
            ->updateMessage($message)
            ->setRating($rating)
            ->setCreatedAt($date);
        return $review;
    }

    public static function leftByGuest(Product $product, string $name, string $message, ?int $rating = 5): self
    {
        $review = new self(new Status(Status::STATUS_WAIT));
        $review
            ->attachProduct($product)
            ->updateName($name)
            ->updateMessage($message)
            ->setRating($rating);
        return $review;
    }

    public static function leftByBuyer(Product $product, Buyer $buyer, string $message, ?int $rating = 5): self
    {
        $review = new self(new Status(Status::STATUS_WAIT));
        $review->assignBuyer($buyer)
            ->attachProduct($product)
            ->updateName($buyer->getInformation()->getName())
            ->updateMessage($message)
            ->setRating($rating);
        return $review;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function updateStatus(Status $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setRating(?int $rating = 5): self
    {
        $this->rating = $rating;
        return $this;
    }

    public function updateName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function assignBuyer(Buyer $buyer): self
    {
        $this->buyer = $buyer;
        return $this;
    }

    public function getBuyer(): ?Buyer
    {
        return $this->buyer;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function attachProduct(Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function updateMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
