<?php

declare(strict_types=1);

namespace App\Model\Quiz\Repository;

use App\Model\Quiz\Entity\Quiz;
use Doctrine\ORM\EntityManagerInterface;

class QuizRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(Quiz::class);
    }

    public function add(Quiz $quiz): void
    {
        $this->em->persist($quiz);
    }

    public function getById(int $id): Quiz
    {
        /** @var Quiz $quiz */
        if (!$quiz = $this->repo->find($id)) {
            throw new \DomainException('quiz.not.found');
        }
        return $quiz;
    }

    public function remove(Quiz $quiz): void
    {
        $this->em->remove($quiz);
    }

    public function getAll()
    {
        return $this->repo->findBy([], ['id' => 'DESC']);
    }
}
