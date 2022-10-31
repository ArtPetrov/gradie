<?php

declare(strict_types=1);

namespace App\Model\Quiz\UseCase\Value\Remove;

use App\Model\Quiz\Entity\QuestValue;
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

    public static function byQuestValue(QuestValue $value): self
    {
        return new self($value->getId());
    }
}
