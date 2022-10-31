<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Repository;

use App\Model\Ecommerce\Entity\Product\Product;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;

class ProductRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(Product::class);
    }

    public function sortByAlphabetically()
    {
        return $this->repo->findBy([], ['popular' => 'ASC','name' => 'ASC',]);
    }

    public function add(Product $element): void
    {
        $this->em->persist($element);
    }

    public function get(int $id): Product
    {
        /** @var Product $element */
        if (!$element = $this->repo->find($id)) {
            throw new \DomainException('product.not.found');
        }
        return $element;
    }

    public function remove(Product $element): void
    {
        $this->em->remove($element);
    }

    public function findByPopular(int $id, int $popular, string $direction): ?int
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('id', 'popular')
            ->from('ecommerce_product')
            ->andWhere('id != :id')
            ->setParameter('id',$id, ParameterType::INTEGER);

        if ($direction === 'up') {
            $stmt->andWhere('popular <= :popular')->orderBy('popular', 'DESC');
        } else {
            $stmt->andWhere('popular >= :popular')->orderBy('popular', 'ASC');
        }
        $stmt->setParameter('popular', $popular, ParameterType::INTEGER);
        $stmt->setMaxResults(1);
        return (int)$stmt->execute()->fetch()['id'];
    }
}
