<?php

declare(strict_types=1);

namespace App\Model\Dealer\ReadModel;

use App\Model\Dealer\Entity\Dealer;
use App\Model\Dealer\Entity\Status;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

class DealerFetcher
{
    private $connection;
    private $paginator;
    private $repository;

    public function __construct(Connection $connection, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->repository = $em->getRepository(Dealer::class);
        $this->paginator = $paginator;
    }

    public function get(int $id): Dealer
    {
        if (!$dealer = $this->repository->find($id)) {
            throw new \DomainException('dealer.not.found');
        }
        return $dealer;
    }

    public function searchByQuery(string $query): ?array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'd.id',
                'd.information_name as name'
            )
            ->from('dealer', 'd')
            ->andWhere('d.moderation_status = :status')
            ->setParameter('status', Status::STATUS_ACTIVE);

        if ($query) {
            $qb->andWhere($qb->expr()->like('LOWER(d.information_name)', ':query'));
            $qb->setParameter(':query', '%' . mb_strtolower($query) . '%');
        }

        $qb->orderBy("d.information_name", 'ASC');
        $qb->setMaxResults(20);

        $stmt = $qb->execute();
        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, ShortDealer::class);
        return $stmt->fetchAll();
    }

}
