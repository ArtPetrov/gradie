<?php

declare(strict_types=1);

namespace App\Model\Quiz\Repository;

use App\Model\Quiz\Entity\Quest;
use App\Model\Quiz\Entity\Quiz;
use Doctrine\ORM\EntityManagerInterface;

class QuestionRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(Quest::class);
    }

    public function add(Quest $quest): void
    {
        $this->em->persist($quest);
    }

    public function getById(int $id): Quest
    {
        /** @var Quest $quest */
        if (!$quest = $this->repo->find($id)) {
            throw new \DomainException('quiz.question.not.found');
        }
        return $quest;
    }

    public function remove(Quest $quest): void
    {
        $this->em->remove($quest);
    }

    public function getAllForQuiz(Quiz $quiz)
    {
        return $this->repo->findBy(['quiz' => $quiz], ['id' => 'ASC']);
    }
}
