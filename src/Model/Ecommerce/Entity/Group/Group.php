<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Entity\Group;

use App\Model\Ecommerce\Entity\Attribute\Attribute;
use App\Model\EventBus;
use App\Model\EventBusTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ecommerce_product_group",
 * indexes={
 *     @ORM\Index(name="product_group_name", columns={"name"}),
 * })
 */
class Group implements EventBus
{
    use EventBusTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var ArrayCollection|Selector[]
     * @ORM\OneToMany(targetEntity="Selector", mappedBy="group", orphanRemoval=true, cascade={"persist","remove"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $selectors;

    /**
     * @var ArrayCollection|Product[]
     * @ORM\OneToMany(targetEntity="Product", mappedBy="group", orphanRemoval=true, cascade={"persist","remove"})
     */
    private $products;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    public function __construct()
    {
        $this->selectors = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    public static function create(string $name): self
    {
        $current = new self();
        $current->name = $name;
        return $current;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function changeName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addSelector(Type $type, string $name, Attribute $attribute, int $position = 0): self
    {
        foreach ($this->selectors as $selector) {
            if ($selector->getAttribute()->getId() === $attribute->getId()) {
                return $this;
            }
        }
        $this->selectors->add(Selector::create($this, $type, $name, $attribute, $position));
        return $this;
    }

    public function getSelectors(): array
    {
        return $this->selectors->toArray();
    }

    public function clearSelectors(): self
    {
        $this->selectors = new ArrayCollection();
        return $this;
    }

    public function clearProducts(): self
    {
        $this->products = new ArrayCollection();
        return $this;
    }

    public function addProduct(\App\Model\Ecommerce\Entity\Product\Product $product): self
    {
        foreach ($this->products as $prod) {
            if ($prod->getProduct()->getId() === $product->getId()) {
                return $this;
            }
        }
        $this->products->add(Product::create($this, $product));
        return $this;
    }

    public function getProducts(): array
    {
        return $this->products->toArray();
    }

    public function toJson(): array
    {
        $selectors = [];
        foreach ($this->selectors as $selector) {
            $selectors[$selector->getAttribute()->getSlug()] = [
                'name' => $selector->getName(),
                'slug' => $selector->getAttribute()->getSlug(),
                'position' => $selector->getPosition(),
                'type' => $selector->getType()->getValue(),
                'values' => $selector->getAttribute()->getValues()->count() > 0 ? $selector->getAttribute()->getValues()->toArray() : [],
            ];
        }

        $products = [];
        foreach ($this->products as $product) {
            $id = $product->getProduct()->getId();
            foreach ($product->getProduct()->getAttributes() as $attr) {
                if (array_key_exists($attr->slug, $selectors)) {
                    $value = $attr->value;
                    foreach ($selectors[$attr->slug]['values'] as $val) {
                        if ($val->value == $value) {
                            $value = $val->label;
                            break;
                        }
                    };
                    $products[$id][$attr->slug] = $value;
                }
            }
        }

        return ['selectors' => $selectors, 'products' => $products];
    }
}
