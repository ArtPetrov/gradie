<?php

declare(strict_types=1);

namespace App\Model\Mailer\Repository;

use App\Model\Mailer\Entity\Recipient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class RecipientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipient::class);
    }

    public function completedForMailer(int $idMailer): int
    {
        return $this->createQueryBuilder('recipient')
            ->select('COUNT(recipient.id)')
            ->andWhere('recipient.mailer = :id')
            ->andWhere('recipient.status = :status')
            ->setParameter(':id', $idMailer)
            ->setParameter(':status', Recipient::STATUS_SENT)
            ->getQuery()->getSingleScalarResult();
    }

    public function totalForMailer(int $idMailer): int
    {
        return $this->createQueryBuilder('recipient')
            ->select('COUNT(recipient.id)')
            ->andWhere('recipient.mailer = :id')
            ->setParameter(':id', $idMailer)
            ->getQuery()->getSingleScalarResult();
    }

    public function recipientForMailer(int $idMailer)
    {
        return $this->findOneBy(['status'=>Recipient::STATUS_WAIT,'mailer'=>$idMailer]);
    }
}
