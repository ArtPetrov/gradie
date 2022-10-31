<?php

declare(strict_types=1);

namespace App\Model\PopularProducts\ReadModel;

use App\Model\PopularProducts\Entity\PopularProducts;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

class PopularFetcher
{
    private $connection;
    private $paginator;
    private $repository;

    public function __construct(Connection $connection, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->repository = $em->getRepository(PopularProducts::class);
        $this->paginator = $paginator;
    }

    public function getAll()
    {
        return $this->repository->findBy([], ['position' => 'DESC']);
    }

    public function getAllWithLimit(int $limit = 6)
    {
        return $this->repository->findBy([], ['position' => 'DESC'], $limit);
    }

    public function get(int $id): PopularProducts
    {
        /** @var PopularProducts $product */
        if (!$product = $this->repository->find($id)) {
            throw new \DomainException('slider.not.found');
        }
        return $product;
    }

    public function findByPosition(int $currentPosition, string $direction): ?int
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('id', 'position')
            ->from('popular_products');

        if ($direction === 'up') {
            $stmt->andWhere('position > :position')->orderBy('position', 'ASC');
        } else {
            $stmt->andWhere('position < :position')->orderBy('position', 'DESC');
        }
        $stmt->setParameter('position', $currentPosition, ParameterType::INTEGER);
        $stmt->setMaxResults(1);
        return (int)$stmt->execute()->fetch()['id'];
    }

}
