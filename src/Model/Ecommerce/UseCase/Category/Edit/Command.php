<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Category\Edit;

use App\Model\Ecommerce\UseCase\Category\Seo;
use App\Model\Ecommerce\Entity\Category\Category;
use App\Model\Ecommerce\Entity\Category\Filter;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\Positive()
     */
    public $id;

    /**
     * @Assert\NotNull(message="not.null")
     */
    public $name;

    public $type;

    public $parent;

    /**
     * @Assert\Valid()
     * @var Seo\Command
     */
    public $seo;

    public $filters;

    public static function fromCategory(Category $category): self
    {
        $command = new self();

        $command->type = $category->getType()->getType();
        $command->id = $category->getId();
        $command->name = $category->getName();
        $command->parent = $category->getParent() !== null ? $category->getParent()->getId() : null;

        $command->filters = new ArrayCollection(array_map(static function ($attr) {
            return Filter::fromCategory($attr->slug, $attr->type, $attr->label, $attr->position);
        }, $category->getFilters()->toArray()));

        $command->seo = Seo\Command::fromCategorySeo($category->getSeo());

        return $command;
    }

}
