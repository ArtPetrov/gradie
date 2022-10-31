<?php

declare(strict_types=1);

namespace App\Model\Quiz\UseCase\Quiz\Remove;

use App\Model\Quiz\Entity\Quiz;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function byQuiz(Quiz $quiz): self
    {
        return new self($quiz->getId());
    }
}
