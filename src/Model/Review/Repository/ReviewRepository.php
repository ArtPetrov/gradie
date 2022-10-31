<?php

declare(strict_types=1);

namespace App\Model\Review\Repository;

use App\Model\Review\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;

class ReviewRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(Review::class);
    }

    public function add(Review $review): void
    {
        $this->em->persist($review);
    }

    public function get(int $id): Review
    {
        /** @var Review $review */
        if (!$review = $this->repo->find($id)) {
            throw new \DomainException('review.not.found');
        }
        return $review;
    }

    public function getAll()
    {
        return $this->repo->findBy([],['createdAt'=>'DESC']);
    }

    public function remove(Review $review): void
    {
        $this->em->remove($review);
    }
}
