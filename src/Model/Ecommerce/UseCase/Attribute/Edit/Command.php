<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Attribute\Edit;

use App\Model\Ecommerce\Entity\Attribute\Attribute;
use App\Model\Ecommerce\UseCase\Attribute\FieldValue;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotNull()
     */
    public $attributeId;

    /**
     * @Assert\NotNull()
     * @Assert\Length(min=2)
     */
    public $name;

    /**
     * @Assert\NotNull()
     */
    public $type;
    public $values;

    public static function fromAttribute(Attribute $attribute): self
    {
        $command = new self();

        $command->attributeId = $attribute->getId();
        $command->type = $attribute->getType()->getTypeAttribute();
        $command->name = $attribute->getName();

        $command->values = new ArrayCollection(array_map(static function ($field) {
            return FieldValue\Command::fromAttribute($field->label, $field->value);
        }, $attribute->getValues()->toArray()));

        return $command;
    }
}
