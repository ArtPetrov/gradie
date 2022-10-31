<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Entity\Group;

use App\Model\Ecommerce\Entity\Attribute\Attribute;
use App\Model\EventBus;
use App\Model\EventBusTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ecommerce_product_group_selector",
 * indexes={
 *     @ORM\Index(name="product_group_selector_position", columns={"position"}),
 * })
 */
class Selector implements EventBus
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
     * @var Type
     * @ORM\Embedded(class="Type")
     */
    private $type;

    /**
     * @var Group
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="selectors")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $group;

    /**
     * @var Attribute
     * @ORM\ManyToOne(targetEntity="App\Model\Ecommerce\Entity\Attribute\Attribute", fetch="EAGER")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $attribute;

    /**
     * @ORM\Column(type="integer", options={"default":0,"unsigned":true})
     */
    private $position = 0;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    public static function create(Group $group, Type $type, string $name, Attribute $attribute, int $position = 0): self
    {
        $current = new self();
        $current->group = $group;
        $current->type = $type;
        $current->name = $name;
        $current->attribute = $attribute;
        $current->position = $position;
        return $current;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function rename(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function changeType(Type $type): self
    {
        $this->type = $type;
        return $this;
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
