<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Group\Product;

use App\Model\Ecommerce\Entity\Product\Product;

class Command
{
    public $id;
    public $name;

    public static function fromGroup(Product $product): self
    {
        $command = new self();
        $command->id = $product->getId();
        $command->name = $product->getInfo()->getName();
        return $command;
    }
}
