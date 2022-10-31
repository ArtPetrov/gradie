<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Attribute\Create;

use App\Model\Ecommerce\Entity\Attribute\Attribute;
use App\Model\Ecommerce\Entity\Attribute\Field;
use App\Model\Ecommerce\Repository\AttributeRepository;
use App\Model\Ecommerce\UseCase\Attribute\FieldValue;
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
        $type = new Field($command->type);

        $attribute = new Attribute($command->name, $type);

        if (Field::SELECT === $type->getTypeAttribute() || Field::CHECKBOX === $type->getTypeAttribute()) {
            FieldValue\Validator::validValues($command->values);
            $attribute->insertValues($command->values);
        }

        $this->attributes->add($attribute);
        $this->flusher->flush();
    }
}
