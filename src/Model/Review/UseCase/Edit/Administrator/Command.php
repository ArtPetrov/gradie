<?php

declare(strict_types=1);

namespace App\Model\Review\UseCase\Edit\Administrator;

use App\Model\Review\Entity\Review;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public $review;
    /**
     * @Assert\NotNull(message="not.null")
     */
    public $status;
    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255,min=2)
     */
    public $name;
    /**
     * @Assert\NotNull(message="not.null")
     */
    public $rating = 5;
    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255,min=10)
     */
    public $message;
    /**
     * @Assert\NotNull(message="not.null")
     */
    public $createAt;

    public static function create(Review $review): self
    {
        $command = new self();
        $command->review = $review->getId();
        $command->status = $review->getStatus()->getStatus();
        $command->name = $review->getName();
        $command->rating = $review->getRating();
        $command->message = $review->getMessage();
        $command->createAt = $review->getCreatedAt();
        return $command;
    }
}
