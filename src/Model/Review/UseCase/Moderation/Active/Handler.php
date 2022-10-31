<?php

declare(strict_types=1);

namespace App\Model\Review\UseCase\Moderation\Active;

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
        $review = $this->reviews->get((int)$command->review);
        $review->updateStatus(new Status(Status::STATUS_ACTIVE));
        $this->flusher->flush($review);
    }
}
