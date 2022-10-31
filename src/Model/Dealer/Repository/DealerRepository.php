<?php

declare(strict_types=1);

namespace App\Model\Dealer\Repository;

use App\Model\Dealer\Entity\Dealer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class DealerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dealer::class);
    }

    public function get(int $id): ?Dealer
    {
        if (!$dealer = $this->find($id)) {
            throw new \DomainException('dealer.not.found');
        }
        return $dealer;
    }

    public function duplicateEmail(string $email, int $currentId = 0): bool
    {
        return $this->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->andWhere('t.email = :email')
                ->andWhere('t.id != :id')
                ->setParameter(':email', $email)
                ->setParameter(':id', $currentId)
                ->getQuery()->getSingleScalarResult() > 0;
    }

    public function findByResetToken(string $token): ?Dealer
    {
        return $this->findOneBy(['resetToken.token' => $token]);
    }

    public function existsByResetToken(string $token): bool
    {
        return $this->createQueryBuilder('tk')
                ->select('COUNT (tk.id)')
                ->where('tk.resetToken.token = :token')
                ->setParameter(':token', $token)
                ->getQuery()->getSingleScalarResult() > 0;
    }

}
