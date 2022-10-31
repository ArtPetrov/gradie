<?php

declare(strict_types=1);

namespace App\Model\Salon\Repository;

use App\Model\Salon\Entity\ModerationSalon;
use App\Model\Salon\Entity\Salon;
use App\Model\Salon\Entity\Status;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;

class ModerationSalonRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(ModerationSalon::class);
    }

    public function add(ModerationSalon $salon): void
    {
        $this->em->persist($salon);
    }

    public function getLastRequestInProcessOfSalon(Salon $salon): ?ModerationSalon
    {
        $qb = $this->em->createQueryBuilder();
        $result = $qb->select('salon')
            ->from(ModerationSalon::class, 'salon')
            ->andWhere('salon.salon = :salon')
            ->setParameter(':salon', $salon)
            ->andWhere('salon.status.status = :process')
            ->setParameter(':process', Status::PROCESS, ParameterType::STRING)
            ->orderBy('salon.id', 'DESC')
            ->getQuery()
            ->execute();
        return count($result) > 0 ? $result[0] : null;
    }

    public function isActualRequestForRemove(Salon $salon):bool
    {
        $qb = $this->em->createQueryBuilder();
        return (int)$qb->select('count(salon)')
                ->from(ModerationSalon::class, 'salon')
                ->andWhere('salon.salon = :salon')
                ->setParameter(':salon', $salon)
                ->andWhere('salon.status.status = :process')
                ->setParameter(':process', Status::PROCESS_DELETE, ParameterType::STRING)
                ->getQuery()->getSingleScalarResult() > 0;
    }

    public function cancelRequestOfSalon(Salon $salon): void
    {
        $qb = $this->em->createQueryBuilder();
        $requests = $qb->select('salon')
            ->from(ModerationSalon::class, 'salon')
            ->andWhere('salon.salon = :salon')
            ->setParameter(':salon', $salon)
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq('salon.status.status', ':process'),
                    $qb->expr()->eq('salon.status.status', ':process_delete')
                ))
            ->setParameter(':process', Status::PROCESS, ParameterType::STRING)
            ->setParameter(':process_delete', Status::PROCESS_DELETE, ParameterType::STRING)
            ->orderBy('salon.id', 'DESC')
            ->getQuery()
            ->execute();

        /** @var ModerationSalon $salon */
        foreach ($requests as $salon) {
            $salon->changeStatus(new Status(Status::CANCEL));
        }
    }

    public function countRequest():int
    {
        $qb = $this->em->createQueryBuilder();
        return  (int) $qb->select('count(salon)')
            ->from(ModerationSalon::class, 'salon')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq('salon.status.status', ':process'),
                    $qb->expr()->eq('salon.status.status', ':process_delete')
                ))
            ->setParameter(':process', Status::PROCESS, ParameterType::STRING)
            ->setParameter(':process_delete', Status::PROCESS_DELETE, ParameterType::STRING)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function get(int $id): ModerationSalon
    {
        /** @var ModerationSalon $salon */
        if (!$salon = $this->repo->find($id)) {
            throw new \DomainException('salon.not.found');
        }
        return $salon;
    }

    public function getAll()
    {
        return $this->repo->findBy([], ['publishedAt' => 'DESC']);
    }

    public function remove(ModerationSalon $salon): void
    {
        $this->em->remove($salon);
    }
}
