<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Group\Selectors;

use App\Model\Ecommerce\Entity\Group\Selector;

class Command
{
    public $slug;
    public $name;
    public $type;
    public $title;
    public $position;

    public static function fromGroup(Selector $selector): self
    {
        $command = new self();
        $command->slug = $selector->getAttribute()->getSlug();
        $command->type = $selector->getType()->getValue();
        $command->title = $selector->getName();
        $command->name = $selector->getAttribute()->getName();
        return $command;
    }
}
