<?php

declare(strict_types=1);

namespace App\Model\Works\Entity;

use App\Model\Ecommerce\Entity\Product\Product;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_works_composition",
 * indexes={
 *     @ORM\Index(name="id_idx_work", columns={"work_id"}),
 *     @ORM\Index(name="position_idx_work", columns={"position"}),
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
     * @ORM\ManyToOne(targetEntity="Work", inversedBy="composition")
     * @ORM\JoinColumn(name="work_id", referencedColumnName="id", nullable=false)
     */
    private $work;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Ecommerce\Entity\Product\Product", fetch="EAGER")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $product;

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

    public function __construct(Work $work, Product $product, int $count, int $position)
    {
        $this->work = $work;
        $this->product = $product;
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

    public function getWork(): Work
    {
        return $this->work;
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
