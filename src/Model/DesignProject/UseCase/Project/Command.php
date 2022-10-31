<?php

declare(strict_types=1);

namespace App\Model\DesignProject\UseCase\Project;

use App\Model\DesignProject\Entity\Information;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $name;

    public $description;

    public static function fromProject(Information $info): self
    {
        $command = new self();
        $command->name = $info->getName();
        $command->description = $info->getDescription();
        return $command;
    }
}
