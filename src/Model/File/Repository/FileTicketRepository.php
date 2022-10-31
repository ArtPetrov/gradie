<?php

declare(strict_types=1);

namespace App\Model\File\Repository;

use App\Model\File\Entity\FileTicket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class FileTicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FileTicket::class);
    }

}
