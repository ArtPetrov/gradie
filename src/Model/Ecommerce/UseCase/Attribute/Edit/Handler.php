<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Attribute\Edit;

use App\Model\Ecommerce\Entity\Attribute;
use App\Model\Ecommerce\Event;
use App\Model\Ecommerce\Helper\ArrayCollectionHelper;
use App\Model\Ecommerce\Repository\AttributeRepository;
use App\Model\Ecommerce\UseCase\Attribute\FieldValue\Validator;
use App\Model\Flusher;

class Handler
{
    private $flusher;
    private $attributes;

    public function __construct(Flusher $flusher, AttributeRepository $attributes)
    {
        $this->flusher = $flusher;
        $this->attributes = $attributes;
    }

    public function handle(Command $command): void
    {
        $attribute = $this->attributes->get($command->attributeId);

        if ($attribute->getName() !== $command->name) {
            $attribute->rename($command->name);
        }

        if ($attribute->getType()->getTypeAttribute() !== $command->type) {
            throw new \DomainException("attribute.type.change.disable");
        }

        if (Attribute\Field::SELECT === $attribute->getType()->getTypeAttribute() ||
            Attribute\Field::CHECKBOX === $attribute->getType()->getTypeAttribute()
        ) {
            if (!ArrayCollectionHelper::equalValues($attribute->getValues(), $command->values)) {
                Validator::validValues($command->values);
                $attribute->rewriteValues($command->values);
            }
        }

        $this->flusher->flush($attribute);
    }
}