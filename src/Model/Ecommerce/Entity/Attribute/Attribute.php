<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Entity\Attribute;

use App\Model\Ecommerce\Event\Attribute\RenamedEvent;
use App\Model\Ecommerce\Event\Attribute\RewriteValuesEvent;
use App\Model\EventBus;
use App\Model\EventBusTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="ecommerce_attribute")
 */
class Attribute implements EventBus
{
    use EventBusTrait;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @ORM\Embedded(class="Field", columnPrefix="field_")
     */
    private $type;

    /**
     * @ORM\Column(type="ecommerce.attribute.values", nullable=true)
     */
    private $values;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    public function __construct(string $name, Field $type)
    {
        $this->name = $name;
        $this->type = $type;
        $this->values = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): Field
    {
        return $this->type;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function rename(string $name): self
    {
        $this->name = $name;
        $this->recordEvent(new RenamedEvent($this->getId(), $this->getSlug()));
        return $this;
    }

    public function changeType(Field $typeField): self
    {
        $this->type = $typeField;
        return $this;
    }

    public function getValues(): ArrayCollection
    {
        return $this->values;
    }

    public function insertValues(ArrayCollection $values): self
    {
        $this->values = $values;
        return $this;
    }

    public function clearValues(): self
    {
        $this->values = new ArrayCollection();
        return $this;
    }

    public function rewriteValues(ArrayCollection $values): self
    {
        $this->recordEvent(new RewriteValuesEvent($this->getId(), clone $values, clone $this->getValues()));
        $this->clearValues()->insertValues($values);
        return $this;
    }

}
