<?php

declare(strict_types=1);

namespace App\Model\Cpanel\Repository;

use App\Model\Cpanel\Entity\Manager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class ManagerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Manager::class);
    }

    public function list()
    {
        return $this->createQueryBuilder('m')
            ->select('m.name, m.id')
            ->orderBy('m.name')
            ->getQuery()
            ->getResult('FetchKeyPair');
    }
}
