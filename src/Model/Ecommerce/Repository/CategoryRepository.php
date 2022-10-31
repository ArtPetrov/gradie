<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Repository;

use App\Model\Ecommerce\Entity\Category\Category;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Tree\Entity\Repository\MaterializedPathRepository;

class CategoryRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(Category::class);
    }

    public function getRepository(): MaterializedPathRepository
    {
        return $this->repo;
    }

    private function makeTree(&$tree, Category $parent, string $basePrefix = null)
    {
        $categoiesChild = $this->getRepository()->getChildren($parent, true, 'position', 'asc');
        if (count($categoiesChild) === 0) {
            return null;
        }
        /** @var Category $category */
        foreach ($categoiesChild as $category) {
            $prefix = str_repeat($basePrefix, $category->getLevel());
            $tree[$prefix . $category->getName()] = $category->getId();
            $this->makeTree($tree, $category, $basePrefix);
        }
    }

    public function getCategoriesForSelect(string $prefix, ?string $header = 'Корневая категория'): array
    {
        $categories = [];
        $rootCategories = $this->getRepository()->getRootNodes('position', 'asc');
        if ($header) {
            $categories[$header] = null;
        }


        /** @var Category $category */
        foreach ($rootCategories as $category) {
            $categories[$prefix . $category->getName()] = $category->getId();
            $this->makeTree($categories, $category, $prefix);
        }

        return $categories;
    }

    public function getCategoriesTree(): array
    {
        $rootCategories = $this->getRepository()->getRootNodes('position', 'asc');
        $categories = [];

        /** @var Category $category */
        foreach ($rootCategories as $category) {
            $categories[] = $category;
        }
        return $categories;
    }

    public function sortByAlphabetically()
    {
        return $this->repo->findBy([], ['name' => 'ASC']);
    }

    public function add(Category $category): void
    {
        $this->em->persist($category);
    }

    public function get(int $id): Category
    {
        /** @var Category $category */
        if (!$category = $this->repo->find($id)) {
            throw new \DomainException('category.not.found');
        }
        return $category;
    }

    public function remove(Category $category): void
    {
        $this->em->remove($category);
    }


}
