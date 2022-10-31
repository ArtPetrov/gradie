<?php

declare(strict_types=1);

namespace App\Model\Review\UseCase\Edit\Administrator;

use App\Model\Flusher;
use App\Model\Review\Entity\Status;
use App\Model\Review\Repository\ReviewRepository;

class Handler
{
    private $flusher;
    private $reviews;

    public function __construct(Flusher $flusher, ReviewRepository $reviews)
    {
        $this->flusher = $flusher;
        $this->reviews = $reviews;
    }

    public function handle(Command $command): void
    {
        $review = $this->reviews->get($command->review);

        $review
            ->updateStatus(new Status($command->status))
            ->updateName($command->name)
            ->setRating($command->rating)
            ->updateMessage($command->message)
            ->setCreatedAt($command->createAt);

        $this->flusher->flush($review);
    }
}
