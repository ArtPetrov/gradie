<?php

declare(strict_types=1);

namespace App\Model\Lead\Repository;

use App\Model\DesignProject\Entity\Status;
use App\Model\Lead\Entity\Lead;
use Doctrine\ORM\EntityManagerInterface;

class LeadRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(Lead::class);
    }

    public function add(Lead $lead): void
    {
        $this->em->persist($lead);
    }

    public function get(int $id): Lead
    {
        /** @var Lead $lead */
        if (!$lead = $this->repo->find($id)) {
            throw new \DomainException('lead.not.found');
        }
        return $lead;
    }

    public function getAll()
    {
        return $this->repo->findBy([], ['createdAt' => 'DESC']);
    }

    public function remove(Lead $lead): void
    {
        $this->em->remove($lead);
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
