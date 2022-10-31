<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Group\Edit;

use App\Model\Ecommerce\Entity\Group\Group;
use App\Model\Ecommerce\Entity\Group\Product;
use App\Model\Ecommerce\Entity\Group\Selector;
use App\Model\Ecommerce\UseCase;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255)
     */
    public $name;

    /**
     * @Assert\NotNull()
     */
    public $id;

    public $selectors;
    public $products;

    public function __construct()
    {
        $this->selectors = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    public static function fromGroup(Group $group): self
    {
        $command = new self();
        $command->id = $group->getId();
        $command->name = $group->getName();

        $command->products = new ArrayCollection(array_map(static function (Product $product): UseCase\Group\Product\Command {
            return UseCase\Group\Product\Command::fromGroup($product->getProduct());
        }, $group->getProducts()));

        $command->selectors = new ArrayCollection(array_map(static function (Selector $selector): UseCase\Group\Selectors\Command {
            return UseCase\Group\Selectors\Command::fromGroup($selector);
        }, $group->getSelectors()));

        return $command;
    }
}
