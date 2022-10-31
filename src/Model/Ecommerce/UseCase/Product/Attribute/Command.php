<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Attribute;

use App\Model\Ecommerce\Entity\Product\Attribute;

class Command
{
    public $slug;
    public $type;
    public $value;
    public $label;
    public $visible;
    public $position;

    public static function fromAttribute(Attribute $attribute): self
    {
        $command = new self();
        $command->slug = $attribute->slug;
        $command->type = $attribute->type;
        $command->value = $attribute->value;
        $command->label = $attribute->label;
        $command->visible = $attribute->visible;
        return $command;
    }
}
