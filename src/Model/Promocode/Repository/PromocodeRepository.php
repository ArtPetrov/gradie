<?php

declare(strict_types=1);

namespace App\Model\Promocode\Repository;

use App\Model\Promocode\Entity\Promocode;
use Doctrine\ORM\EntityManagerInterface;

class PromocodeRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(Promocode::class);
    }

    public function add(Promocode $promo): void
    {
        $this->em->persist($promo);
    }

    public function getByCode(string $code): Promocode
    {
        /** @var Promocode $promo */
        if (!$promo = $this->repo->findOneBy(['information.code' => $code])) {
            throw new \DomainException('promocode.not.found');
        }
        return $promo;
    }

    public function get(int $id): Promocode
    {
        /** @var Promocode $promo */
        if (!$promo = $this->repo->find($id)) {
            throw new \DomainException('promocode.not.found');
        }
        return $promo;
    }

    public function getAll()
    {
        return $this->repo->findBy([], ['id' => 'DESC']);
    }

    public function remove(Promocode $promo): void
    {
        $this->em->remove($promo);
    }

    public function hasCode(string $code): bool
    {
        return $this->repo->createQueryBuilder('promocode')
                ->select('COUNT(promocode)')
                ->andWhere('promocode.information.code = :code')
                ->setParameter(':code', mb_strtoupper($code))
                ->getQuery()->getSingleScalarResult() > 0;
    }
}
