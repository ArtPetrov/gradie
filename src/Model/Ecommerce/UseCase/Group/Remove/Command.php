<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Group\Remove;

use App\Model\Ecommerce\Entity\Group\Group;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $id;

    public static function fromGroup(Group $group): self
    {
        $command = new self();
        $command->id = $group->getId();
        return $command;
    }
}
