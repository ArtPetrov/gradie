<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Entity\Product;

use App\Model\Ecommerce\Entity\Category\Category as EntityCategory;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ecommerce_product_category",
 * indexes={
 *     @ORM\Index(name="product_idx_cat", columns={"product_id"}),
 *     @ORM\Index(name="product_main_idx_cat", columns={"product_id", "main"}),
 * })
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="categories")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", unique=false,  nullable=false)
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Ecommerce\Entity\Category\Category", fetch="EAGER")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", unique=false, nullable=false, onDelete="CASCADE")
     */
    private $category;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default":false})
     */
    private $main;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    public function __construct(EntityCategory $category, Product $product, bool $main)
    {
        $this->product = $product;
        $this->category = $category;
        $this->main = $main;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getCategory(): EntityCategory
    {
        return $this->category;
    }

    public function isMain(): bool
    {
        return $this->main;
    }

    public function enableMain(): self
    {
        $this->main = true;
        return $this;
    }

    public function disableMain(): self
    {
        $this->main = false;
        return $this;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

}