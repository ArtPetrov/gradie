<?php

declare(strict_types=1);

namespace App\Model\News\Repository;

use App\Model\News\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    public function findSortPublished()
    {
        return $this->findBy([], ['publishedAt' => 'DESC']);
    }

    public function findActiveNews()
    {
        return $this->createQueryBuilder('news')
            ->andWhere('news.publishedAt <= :date')
            ->setParameter(':date', new \DateTimeImmutable())
            ->orderBy('news.publishedAt', 'DESC')
            ->getQuery();
    }
}
