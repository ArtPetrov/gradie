<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\ReadModel\Group;

use App\Model\Ecommerce\Entity\Group\Group;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class GroupFetcher
{
    private $connection;
    private $paginator;
    private $repository;

    public function __construct(Connection $connection, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->repository = $em->getRepository(Group::class);
        $this->paginator = $paginator;
    }

    public function findByName(?string $query, int $page, int $limit): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('gp.id, gp.name, 
            (SELECT count(*) FROM ecommerce_product_group_link_on_product p WHERE gp.id=p.group_id) as products, 
            (SELECT count(*) FROM ecommerce_product_group_selector s WHERE gp.id=s.group_id) as selectors')
            ->from('ecommerce_product_group', 'gp');
        if ($query) {
            $qb->andWhere('LOWER(gp.name) LIKE :query');
            $qb->setParameter(':query', '%' . mb_strtolower($query) . '%');
        }
        $qb->orderBy("gp.name", 'ASC');
        return $this->paginator->paginate($qb, $page, $limit);
    }
}
