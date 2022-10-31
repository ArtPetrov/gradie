<?php

declare(strict_types=1);

namespace App\Model\Review\UseCase\Moderation\Active;

use App\Model\Review\Entity\Review;

class Command
{
    public $review;

    public function __construct(Review $review)
    {
        $this->review = $review->getId();
    }
}