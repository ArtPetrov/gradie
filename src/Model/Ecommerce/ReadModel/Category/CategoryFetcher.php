<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\ReadModel\Category;

use App\Model\Ecommerce\Entity\Category\Category;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;

class CategoryFetcher
{
    private $connection;
    private $repository;

    public function __construct(Connection $connection, EntityManagerInterface $em)
    {
        $this->connection = $connection;
        $this->repository = $em->getRepository(Category::class);

    }

    public function getByPath(string $path): Category
    {
        if (!$category = $this->repository->findOneBy(['path'=>$path])) {
            throw new \DomainException('category.not.found');
        }
        return $category;
    }

    public function childsCategory(?int $parent = null): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'category.id',
                'category.name',
                'category.type',
                'category.path',
                )
            ->from('ecommerce_category', 'category');

        if ($parent) {
            $qb->where('category.parent_id = :parent')
                ->setParameter('parent', $parent, ParameterType::INTEGER);
        } else {
            $qb->where('category.parent_id IS NULL');
        }

        $stmt = $qb->orderBy("category.position", 'ASC')
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, CategoryHeader::class);
        return $stmt->fetchAll();
    }
}
