<?php

declare(strict_types=1);

namespace App\Model\Basket\Entity;

use App\Helper\BasketTokenInterface;
use App\Model\Ecommerce\Entity\Product\Product;
use App\Model\EventBus;
use App\Model\EventBusTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="basket_items", indexes={
 *     @ORM\Index(columns={"token"}),
 * }, uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"token", "product_id"})
 * })
 */
class Item implements EventBus
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
     * @var BasketTokenInterface
     * @ORM\Embedded(class="BasketToken", columnPrefix=false)
     */
    private $token;

    /**
     * @var Product
     * @ORM\ManyToOne(targetEntity="App\Model\Ecommerce\Entity\Product\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $product;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true, "default":1})
     */
    private $count = 1;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    public static function create(BasketTokenInterface $token, Product $product, int $count): self
    {
        $item = new self();
        $item->token = $token;
        $item->product = $product;
        $item->count = $count;
        return $item;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function updateCount(int $count): self
    {
        $this->count = $count;
        return $this;
    }
}
