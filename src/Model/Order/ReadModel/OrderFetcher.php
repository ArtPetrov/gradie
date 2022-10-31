<?php

declare(strict_types=1);

namespace App\Model\Order\ReadModel;

use App\Helper\BasketTokenInterface;
use App\Model\Cpanel\UseCase\Filter\Orders\Filter;
use App\Model\Order\Entity\Order;
use App\Model\Order\Entity\Status;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class OrderFetcher
{
    private $connection;
    private $paginator;
    private $em;

    public function __construct(Connection $connection, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->em = $em;
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    public function all(Filter $filter, int $currentPage, int $countElement): PaginationInterface
    {
        $qb = $this->em->createQueryBuilder()
            ->select('o')
            ->from(Order::class, 'o');

        if ($filter->query) {
            $qb->andWhere(
                $qb->expr()->orX(
                $qb->expr()->eq('o.id', ':id'),
                $qb->expr()->like('LOWER(o.contact.name)', ':query'),
                $qb->expr()->like('LOWER(o.contact.phone)', ':query'),
                $qb->expr()->like('LOWER(o.contact.email)', ':query')
                )
            );
            $qb->setParameter(':id', (int)$filter->query, ParameterType::INTEGER);
            $qb->setParameter(':query', '%' . mb_strtolower($filter->query) . '%', ParameterType::STRING);
        }

        if ($filter->status) {
            $qb->andWhere('o.status.status = :status');
            $qb->setParameter(':status', $filter->status);
        }

        $qb->orderBy('o.id', 'DESC');
        return $this->paginator->paginate($qb, $currentPage, $countElement);
    }

    public function findByTokenForCabinet(BasketTokenInterface $token, int $currentPage, int $countElement): PaginationInterface
    {
        $qb = $this->em->createQueryBuilder()
            ->select('o')
            ->from(Order::class, 'o');

        $qb->andWhere('o.basket.token = :token');
        $qb->setParameter(':token', $token->getToken(), ParameterType::STRING);

        $qb->andWhere('o.status.status IN (:status)');
        $qb->setParameter(':status', Status::getStatusForShowClient(), Connection::PARAM_STR_ARRAY);

        $qb->orderBy('o.id', 'DESC');
        return $this->paginator->paginate($qb, $currentPage, $countElement);
    }
}
