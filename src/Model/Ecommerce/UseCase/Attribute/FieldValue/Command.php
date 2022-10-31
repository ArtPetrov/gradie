<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Attribute\FieldValue;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\Length(min=1)
     */
    public $label;

    /**
     * @Assert\Length(min=1)
     */
    public $value;

    public static function fromAttribute(string $label, string $value): self
    {
        $command = new self();
        $command->label = $label;
        $command->value = $value;
        return $command;
    }

}
