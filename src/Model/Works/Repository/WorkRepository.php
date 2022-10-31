<?php

declare(strict_types=1);

namespace App\Model\Works\Repository;

use App\Model\Works\Entity\Work;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;

class WorkRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(Work::class);
    }

    public function add(Work $work): void
    {
        $this->em->persist($work);
    }

    public function get(int $id): Work
    {
        /** @var Work $work */
        if (!$work = $this->repo->find($id)) {
            throw new \DomainException('work.not.found');
        }
        return $work;
    }

    public function getAll()
    {
        return $this->repo->findBy([],['position'=>'DESC']);
    }

    public function remove(Work $work): void
    {
        $this->em->remove($work);
    }

    public function findByPosition(int $currentPosition, string $direction): ?int
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('id', 'position')
            ->from('content_works');

        if ($direction === 'up') {
            $stmt->andWhere('position > :position')->orderBy('position', 'ASC');
        } else {
            $stmt->andWhere('position < :position')->orderBy('position', 'DESC');
        }
        $stmt->setParameter('position', $currentPosition, ParameterType::INTEGER);
        $stmt->setMaxResults(1);
        return (int)$stmt->execute()->fetch()['id'];
    }
}
