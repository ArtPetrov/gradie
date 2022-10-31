<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Category\Edit;

use App\Model\Ecommerce\Entity\Category\Category;
use App\Model\Ecommerce\Entity\Category\Seo;
use App\Model\Ecommerce\Entity\Category\Type;
use App\Model\Ecommerce\Helper\ArrayCollectionHelper;
use App\Model\Ecommerce\Repository\CategoryRepository;
use App\Model\Flusher;

class Handler
{
    private $flusher;
    private $categories;

    public function __construct(Flusher $flusher, CategoryRepository $categories)
    {
        $this->flusher = $flusher;
        $this->categories = $categories;
    }

    public function handle(Command $command): void
    {
        $category = $this->categories->get($command->id);

        $category->setType(new Type($command->type));

        if ($category->getName() !== $command->name) {
            $category->changeName($command->name);
        }

        $category->updateSeo(new Seo($command->seo->title,
            $command->seo->keywords,
            $command->seo->description,
            $command->seo->content
        ));

        if (!ArrayCollectionHelper::equalValues($category->getFilters(), $command->filters)) {
            $category->reloadFilters($command->filters);
        }

        if ($command->parent === $command->id || !$this->checkValidHierarchy($category, $command->parent)) {
            throw new \DomainException("category.incorrect.hierarchy.parent");
        }

        if ($command->parent !== $category->getParent()) {
            $parent = $command->parent > 0 ? $this->categories->get($command->parent) : null;
            $category->setParent($parent);
        }

        $this->flusher->flush();
    }

    private function checkValidHierarchy(Category $category, ?int $parentCategory): bool
    {
        $children = $this->categories->getRepository()->getChildren($category);
        /** @var Category $child */
        foreach ($children as $child) {
            if ($child->getId() === $parentCategory) {
                return false;
            }
        }
        return true;
    }

}
