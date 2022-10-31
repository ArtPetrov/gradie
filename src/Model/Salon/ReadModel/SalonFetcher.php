<?php

declare(strict_types=1);

namespace App\Model\Salon\ReadModel;

use App\Model\Dealer\Entity\Dealer;
use App\Model\Salon\Entity\ModerationSalon;
use App\Model\Salon\Entity\Owners;
use App\Model\Salon\Entity\Salon;
use App\Model\Salon\Entity\Status;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;

class SalonFetcher
{
    private $connection;
    private $repository;
    private $em;

    public function __construct(Connection $connection, EntityManagerInterface $em)
    {
        $this->connection = $connection;
        $this->repository = $em->getRepository(Salon::class);
        $this->em = $em;
    }

    public function getSalons(): ?array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'salon.id',
                'salon.type',
                'salon.info_name as name',
                'salon.info_address as address',
                'salon.info_timetable as timetable',
                'salon.info_phone as phone',
                'salon.info_email as email',
                'salon.info_site as site',
                'salon.info_comment as comment',
                'salon.coord_lat as lat',
                'salon.coord_lon as lon'
            )
            ->from('salon', 'salon')
            ->orderBy("salon.info_name", 'ASC')
            ->execute();
        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, SalonCard::class);
        return $stmt->fetchAll();
    }

    public function getSalonsWithModeration(): ?array
    {
        $qb = $this->em->createQueryBuilder();

        $result = $qb->select(
            'salon.id',
            'salon.type.type as type',
            'salon.info.name as name',
            'salon.info.address as address',
            'moderation.status.status as moderation_status',
            'dealer.information.name as dealer_name'
        )
            ->from(Salon::class, 'salon')
            ->leftJoin(Owners::class, 'owner', Expr\Join::WITH, 'salon = owner.salon')
            ->leftJoin(Dealer::class,'dealer',Expr\Join::WITH, 'dealer = owner.dealer')
            ->leftJoin(ModerationSalon::class, 'moderation', Expr\Join::WITH,
                $qb->expr()->andX(
                    $qb->expr()->eq('moderation.salon', 'salon'),
                    $qb->expr()->orX(
                        $qb->expr()->eq('moderation.status.status', ':process'),
                        $qb->expr()->eq('moderation.status.status', ':process_delete')
                    )))
            ->setParameter(':process', Status::PROCESS, ParameterType::STRING)
            ->setParameter(':process_delete', Status::PROCESS_DELETE, ParameterType::STRING)
            ->OrderBy('moderation.createdAt', 'ASC')->addOrderBy('salon.createdAt', 'DESC')
            ->getQuery()
            ->execute();
        return $result;
    }

    public function getSalonsWithOutDealer()
    {
        return $this->em->createQueryBuilder()
            ->select('salon')
            ->from(Salon::class, 'salon')
            ->leftJoin(Owners::class, 'dealer', Expr\Join::WITH, 'dealer.salon = salon.id')
            ->andWhere('dealer.id IS NULL')
            ->groupBy('salon.id')
            ->orderBy('salon.id', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function getSalonsForDealer(Dealer $dealer)
    {
        return $this->em->createQueryBuilder()
            ->select('salon')
            ->from(Salon::class, 'salon')
            ->leftJoin(Owners::class, 'dealer', Expr\Join::WITH, 'dealer.salon = salon.id')
            ->andWhere('dealer.dealer = :dealer')
            ->setParameter(':dealer', $dealer->getId(), ParameterType::INTEGER)
            ->leftJoin(ModerationSalon::class, 'moderation', Expr\Join::WITH, 'moderation.salon = salon.id AND moderation.status.status = :status ')
            ->andWhere('moderation.status.status <> :status OR moderation.status.status IS NULL')
            ->setParameter(':status', Status::PROCESS_DELETE, ParameterType::STRING)
            ->groupBy('salon.id')
            ->orderBy('salon.id', 'DESC')
            ->getQuery()
            ->execute();
    }
}
