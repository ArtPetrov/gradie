<?php

declare(strict_types=1);

namespace App\Model\Quiz\UseCase\Results\Remove;

use App\Model\Quiz\Entity\QuizResult;
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

    public static function byQuestValue(QuizResult $result): self
    {
        return new self($result->getId());
    }
}
