<?php

declare(strict_types=1);

namespace App\Model\Promocode\UseCase\Edit;

use Symfony\Component\Validator\Constraints as Assert;
use App\Model\Promocode\Entity;

class Information
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="2")
     */
    public $code;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="2")
     */
    public $name;

    /**
     * @Assert\Length(min="5")
     */
    public $description;

    public static function fromPromocode(Entity\Information $info): self
    {
        $command = new self();
        $command->code = $info->getCode();
        $command->name = $info->getName();
        $command->description = $info->getDescription();
        return $command;
    }
}
