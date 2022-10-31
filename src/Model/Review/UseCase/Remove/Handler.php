<?php

declare(strict_types=1);

namespace App\Model\Review\UseCase\Remove;

use App\Model\Flusher;
use App\Model\Review\Repository\ReviewRepository;

class Handler
{
    private $reviews;
    private $flusher;

    public function __construct(ReviewRepository $reviews, Flusher $flusher)
    {
        $this->reviews = $reviews;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $review = $this->reviews->get($command->id);
        $this->reviews->remove($review);
        $this->flusher->flush();
    }
}
