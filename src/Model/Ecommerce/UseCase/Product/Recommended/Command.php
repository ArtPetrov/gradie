<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Recommended;

use App\Model\Ecommerce\Entity\Product\Recommended;

class Command
{
    public $id;
    public $name;
    public $position;

    public static function fromRecommended(Recommended $recommended): self
    {
        $command = new self();
        $command->id = $recommended->getRecommended()->getId();
        $command->name = $recommended->getRecommended()->getInfo()->getName();
        return $command;
    }
}
