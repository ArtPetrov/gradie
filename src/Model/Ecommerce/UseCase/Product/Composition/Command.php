<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Composition;

use App\Model\Ecommerce\Entity\Product\Composition;

class Command
{
    public $id;
    public $name;
    public $count;
    public $position;

    public static function fromComposition(Composition $product): self
    {
        $command = new self();
        $command->id = $product->getElement()->getId();
        $command->name = $product->getElement()->getInfo()->getName();
        $command->count = $product->getCount();
        return $command;
    }
}
