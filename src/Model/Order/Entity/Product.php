<?php

declare(strict_types=1);

namespace App\Model\Order\Entity;

use App\Model\EventBus;
use App\Model\EventBusTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="order_product",
 * indexes={
 *     @ORM\Index(name="order_id_idx", columns={"order_id"})
 * })
 */
class Product implements EventBus
{
    use EventBusTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Order
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="products")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @var int
     * @ORM\Column(type="integer", name="product_id", options={"unsigned"=true})
     */
    private $product;

    /**
     * @var int
     * @ORM\Column(type="integer",  options={"default": 1, "unsigned"=true})
     */
    private $count;

    /**
     * @var string
     * @ORM\Column(type="string", length=128)
     */
    private $article;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2, options={"default":0.00, "unsigned"=true})
     */
    private $price;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=3, options={"default":0.00, "unsigned"=true})
     */
    private $volume;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=3, options={"default":0.00, "unsigned"=true})
     */
    private $weight;

    /**
     * @var int
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    public static function create(Order $order, \App\Model\Ecommerce\Entity\Product\Product $product, int $count = 1): self
    {
        $command = new self();
        $command->order = $order;
        $command->product = $product->getId();
        $command->count = $count;
        $command->article = $product->getInfo()->getArticle();
        $command->name = $product->getInfo()->getName();
        $command->price = $product->getFinishPrice();
        $command->weight = $product->getFinishWeight();
        $command->volume = $product->getFinishVolume();
        return $command;
    }

    public function getId(): int
    {
        return (int)$this->id;
    }

    public function getCount(): int
    {
        return (int)$this->count;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getProductId(): int
    {
        return (int)$this->product;
    }

    public function getVersion(): int
    {
        return (int)$this->version;
    }

    public function getPrice(): float
    {
        return (float)$this->price;
    }

    public function getArticle(): string
    {
        return $this->article;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVolume(): float
    {
        return (float)$this->volume;
    }

    public function getWeight(): float
    {
        return (float)$this->weight;
    }
}
