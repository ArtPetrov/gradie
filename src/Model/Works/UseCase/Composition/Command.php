<?php

declare(strict_types=1);

namespace App\Model\Works\UseCase\Composition;

use App\Model\Works\Entity\Composition;

class Command
{
    public $id;
    public $name;
    public $count;
    public $position;

    public static function fromWork(Composition $product): self
    {
        $command = new self();
        $command->id = $product->getProduct()->getId();
        $command->name = $product->getProduct()->getInfo()->getName();
        $command->count = $product->getCount();
        return $command;
    }
}
