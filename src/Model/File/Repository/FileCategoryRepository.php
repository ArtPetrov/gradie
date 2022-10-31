<?php

declare(strict_types=1);

namespace App\Model\File\Repository;

use App\Model\File\Entity\FileCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class FileCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FileCategory::class);
    }

    public function findById(int $id)
    {
        return $this->findBy(['category' => $id],['position'=>'DESC']);
    }
}
