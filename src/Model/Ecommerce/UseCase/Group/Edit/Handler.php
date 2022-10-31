<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Group\Edit;

use App\Model\Ecommerce\Entity\Group\Type;
use App\Model\Ecommerce\Repository\AttributeRepository;
use App\Model\Ecommerce\Repository\GroupRepository;
use App\Model\Ecommerce\Repository\ProductRepository;
use App\Model\Flusher;

class Handler
{
    private $flusher;
    private $groups;
    private $products;
    private $attributes;

    public function __construct(Flusher $flusher, GroupRepository $groups, ProductRepository $products, AttributeRepository $attributes)
    {
        $this->flusher = $flusher;
        $this->groups = $groups;
        $this->products = $products;
        $this->attributes = $attributes;
    }

    public function handle(Command $command): void
    {
        $group = $this->groups->get((int)$command->id);
        $group->changeName($command->name);

        $group->clearProducts()->clearSelectors();

        foreach ($command->products->getValues() as $product) {
            $group->addProduct($this->products->get((int)$product->id));
        }

        foreach ($command->selectors->getValues() as $selector) {
            $group->addSelector(
                new Type($selector->type),
                $selector->title,
                $this->attributes->getBySlug($selector->slug),
                (int)$selector->position
            );
        }

        $this->flusher->flush($group);
    }
}
