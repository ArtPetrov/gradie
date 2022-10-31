<?php

declare(strict_types=1);

namespace App\Model\Quiz\Repository;

use App\Model\Quiz\Entity\Quest;
use App\Model\Quiz\Entity\QuestValue;
use Doctrine\ORM\EntityManagerInterface;

class ValueRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(QuestValue::class);
    }

    public function add(QuestValue $value): void
    {
        $this->em->persist($value);
    }

    public function getById(int $id): QuestValue
    {
        /** @var QuestValue $value */
        if (!$value = $this->repo->find($id)) {
            throw new \DomainException('quiz.question.value.not.found');
        }
        return $value;
    }

    public function remove(QuestValue $value): void
    {
        $this->em->remove($value);
    }

    public function getAllForQuest(Quest $quest)
    {
        return $this->repo->findBy(['quest' => $quest], ['id' => 'ASC']);
    }
}
