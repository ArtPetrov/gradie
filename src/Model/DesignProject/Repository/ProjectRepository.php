<?php

declare(strict_types=1);

namespace App\Model\DesignProject\Repository;

use App\Model\DesignProject\Entity\Project;
use App\Model\DesignProject\Entity\Status;
use Doctrine\ORM\EntityManagerInterface;

class ProjectRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(Project::class);
    }

    public function add(Project $project): void
    {
        $this->em->persist($project);
    }

    public function get(int $id): Project
    {
        /** @var Project $project */
        if (!$project = $this->repo->find($id)) {
            throw new \DomainException('project.not.found');
        }
        return $project;
    }

    public function getAll()
    {
        return $this->repo->findBy([], ['createdAt' => 'DESC']);
    }

    public function remove(Project $project): void
    {
        $this->em->remove($project);
    }

    public function getForRangeDate(\DateTimeInterface $start, \DateTimeInterface $end):array
    {
        return $this->repo->createQueryBuilder('project')
            ->select('project')
            ->andWhere('project.createdAt >= :start')
            ->andWhere('project.createdAt <= :end')
            ->setParameter(':start', $start)
            ->setParameter(':end', $end)
            ->orderBy('project.createdAt', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function countNew(): int
    {
        return (int)$this->repo->createQueryBuilder('w')
            ->select('COUNT(w.id)')
            ->andWhere('w.status.status = :status')
            ->setParameter(':status', Status::NEW)
            ->getQuery()->getSingleScalarResult();
    }

}
