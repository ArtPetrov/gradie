<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Entity\Product;

use App\Model\File\Entity\File;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ecommerce_product_images",
 * indexes={
 *     @ORM\Index(name="position_idx_img", columns={"position"}),
 *     @ORM\Index(name="product_idx_img", columns={"product_id"}),
 *     @ORM\Index(name="product_cover_idx_img", columns={"product_id", "cover"}),
 * })
 */
class Image
{
    public const DIRECTORY_FILES = 'products';
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="images")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id",nullable=false)
     */
    private $product;

    /**
     * @ORM\OneToOne(targetEntity="App\Model\File\Entity\File", cascade = {"persist","remove"}, orphanRemoval=true, fetch="EAGER")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", nullable=false)
     */
    private $file;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default":false})
     */
    private $cover;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default":0,"unsigned":true})
     */
    private $position;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    public function __construct(Product $product, File $file, bool $cover, int $position = 0)
    {
        $this->product = $product;
        $this->file = $file;
        $this->cover = $cover;
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

    public function getFile(): File
    {
        return $this->file;
    }

    public function isCover(): bool
    {
        return $this->cover;
    }

    public function setCover(bool $cover): self
    {
        $this->cover = $cover;
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