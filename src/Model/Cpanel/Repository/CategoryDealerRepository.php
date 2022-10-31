<?php

declare(strict_types=1);

namespace App\Model\Cpanel\Repository;

use App\Model\Cpanel\Entity\CategoryDealer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ManagerRegistry;

class CategoryDealerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryDealer::class);
    }

    public function findByPosition(int $currentPosition, string $direction)
    {
        $criteria = Criteria::create();
        $field = 'position';
        if ($direction === 'up') {
            $criteria->andWhere(Criteria::expr()->gt($field, $currentPosition))->orderBy(['position' => 'ASC']);
        } else {
            $criteria->andWhere(Criteria::expr()->lt($field, $currentPosition))->orderBy(['position' => 'DESC']);
        }
        $criteria->setMaxResults(1);
        $friendlyCategory = $this->matching($criteria)->first();
        return $friendlyCategory;
    }

    public function findById(int $id)
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function findAllSortPosition()
    {
        return $this->findBy([], ['position' => 'DESC']);
    }

    public function list()
    {
        return $this->createQueryBuilder('c')
            ->select('c.name, c.id')
            ->orderBy('c.position', 'DESC')
            ->getQuery()
            ->getResult('FetchKeyPair');
    }

}
