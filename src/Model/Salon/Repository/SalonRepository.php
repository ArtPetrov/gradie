<?php

declare(strict_types=1);

namespace App\Model\Salon\Repository;

use App\Model\Salon\Entity\Salon;
use Doctrine\ORM\EntityManagerInterface;

class SalonRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(Salon::class);
    }

    public function add(Salon $salon): void
    {
        $this->em->persist($salon);
    }

    public function get(int $id): Salon
    {
        /** @var Salon $salon */
        if (!$salon = $this->repo->find($id)) {
            throw new \DomainException('salon.not.found');
        }
        return $salon;
    }

    public function getAll()
    {
        return $this->repo->findBy([],['createdAt'=>'DESC']);
    }

    public function remove(Salon $salon): void
    {
        $this->em->remove($salon);
    }
}
