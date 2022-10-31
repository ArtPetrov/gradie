<?php

declare(strict_types=1);

namespace App\Model\Quiz\Repository;

use App\Model\Quiz\Entity\QuizResult;
use App\Model\Quiz\Entity\Status;
use Doctrine\ORM\EntityManagerInterface;

class ResultRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(QuizResult::class);
    }

    public function add(QuizResult $result): void
    {
        $this->em->persist($result);
    }

    public function getById(int $id): QuizResult
    {
        /** @var QuizResult $result */
        if (!$result = $this->repo->find($id)) {
            throw new \DomainException('quiz.result.not.found');
        }
        return $result;
    }

    public function remove(QuizResult $result): void
    {
        $this->em->remove($result);
    }

    public function getAll()
    {
        return $this->repo->findBy([], ['id' => 'DESC']);
    }

    public function countNew(): int
    {
        return (int)$this->repo->createQueryBuilder('w')
            ->select('COUNT(w.id)')
            ->andWhere('w.status.status IN (:status)')
            ->setParameter(':status', [Status::NEW])
            ->getQuery()->getSingleScalarResult();
    }
}
