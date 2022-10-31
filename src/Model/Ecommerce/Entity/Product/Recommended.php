<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Entity\Product;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ecommerce_product_recommended",
 * indexes={
 *     @ORM\Index(name="position_idx_rmd", columns={"position"}),
 *     @ORM\Index(name="product_idx_rmd", columns={"product_id"}),
 * })
 */
class Recommended
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="recommended")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=false)
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumn(name="recommened_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $recommended;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default":0,"unsigned":true})
     */
    private $position;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    public function __construct(Product $product, Product $parentProduct, int $position)
    {
        $this->product = $product;
        $this->recommended = $parentProduct;
        $this->position = $position;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function getRecommended(): Product
    {
        return $this->recommended;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
