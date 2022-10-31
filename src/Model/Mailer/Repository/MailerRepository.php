<?php

declare(strict_types=1);

namespace App\Model\Mailer\Repository;

use App\Model\Mailer\Entity\Mailer;
use App\Model\Mailer\Entity\Process;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class MailerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mailer::class);
    }

    public function activeMailer()
    {
        return $this->findOneBy(['process.status'=>Process::STATUS_WORK],['id'=>'ASC']);
    }
}
