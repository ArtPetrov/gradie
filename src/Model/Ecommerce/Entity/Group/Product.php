<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Entity\Group;

use App\Model\EventBus;
use App\Model\EventBusTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ecommerce_product_group_link_on_product")
 */
class Product implements EventBus
{
    use EventBusTrait;

    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Group
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="products")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $group;

    /**
     * @var \App\Model\Ecommerce\Entity\Product\Product
     * @ORM\ManyToOne(targetEntity="App\Model\Ecommerce\Entity\Product\Product", fetch="EAGER")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $product;

    public static function create(Group $group, \App\Model\Ecommerce\Entity\Product\Product $product): self
    {
        $current = new self();
        $current->group = $group;
        $current->product = $product;
        return $current;
    }

    public function getProduct(): \App\Model\Ecommerce\Entity\Product\Product
    {
        return $this->product;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }
}
