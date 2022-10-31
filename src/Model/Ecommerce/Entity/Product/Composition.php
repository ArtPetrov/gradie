<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Entity\Product;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ecommerce_product_composition",
 * indexes={
 *     @ORM\Index(name="element_idx_cp", columns={"element_id"}),
 *     @ORM\Index(name="product_idx_cp", columns={"product_id"}),
 *     @ORM\Index(name="position_idx_cp", columns={"position"}),
 * })
 */
class Composition
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="composition")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=false)
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="Product", fetch="EAGER")
     * @ORM\JoinColumn(name="element_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $element;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default":1,"unsigned":true})
     */
    private $count;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default":0,"unsigned":true})
     */
    private $position;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    public function __construct(Product $product, Product $element, int $count, int $position)
    {
        $this->product = $product;
        $this->element = $element;
        $this->count = $count;
        $this->position = $position;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getElement(): Product
    {
        return $this->element;
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