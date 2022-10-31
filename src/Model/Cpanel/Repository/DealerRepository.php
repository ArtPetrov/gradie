<?php

declare(strict_types=1);

namespace App\Model\Cpanel\Repository;

use App\Model\Cpanel\UseCase\Filter\Dealers\Filter;
use App\Model\Dealer\Entity\Dealer;
use App\Model\Dealer\Entity\Status;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class DealerRepository extends ServiceEntityRepository
{
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Dealer::class);
        $this->paginator = $paginator;
    }

    public function countWaitDealers(): int
    {
        return (int)$this->createQueryBuilder('w')
            ->select('COUNT(w.id)')
            ->andWhere('w.status.status = :status')
            ->setParameter(':status', Status::STATUS_WAIT)
            ->getQuery()->getSingleScalarResult();
    }

    public function all(Filter $filter, int $currentPage, int $countElement): PaginationInterface
    {
        $qb = $this->createQueryBuilder('dealer')
            ->leftJoin('dealer.category', 'category')
            ->addSelect('category')
            ->leftJoin('dealer.manager', 'manager')
            ->addSelect('manager');

        if ($filter->query) {
            $qb->andWhere('LOWER(dealer.email) LIKE :query OR LOWER(dealer.information.name) LIKE :query');
            $qb->setParameter(':query', '%' . mb_strtolower($filter->query) . '%');
        }

        if ($filter->status) {
            $qb->andWhere('dealer.status.status = :status');
            $qb->setParameter(':status', $filter->status);
        }

        if ($filter->category) {
            $qb->andWhere('category.id = :category');
            $qb->setParameter(':category', $filter->category);
        }

        if ($filter->manager) {
            $qb->andWhere('manager.id = :manager');
            $qb->setParameter(':manager', $filter->manager);
        }

        $qb->orderBy('dealer.id', 'DESC');

        return $this->paginator->paginate($qb, $currentPage, $countElement);
    }


}
