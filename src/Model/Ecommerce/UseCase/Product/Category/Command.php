<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Category;

use App\Model\Ecommerce\Entity\Product\Category;

class Command
{
    public $main;
    public $categories = [];

    public static function fromCategories(array $categories): self
    {
        $command = new self();

        /** @var Category $category */
        foreach ($categories as $category) {
            if ($category->isMain()) {
                $command->main = $category->getCategory()->getId();
            }
            $command->categories[] = $category->getCategory()->getId();
        }
        return $command;
    }
}
