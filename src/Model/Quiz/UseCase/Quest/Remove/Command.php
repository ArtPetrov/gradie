<?php

declare(strict_types=1);

namespace App\Model\Quiz\UseCase\Quest\Remove;

use App\Model\Quiz\Entity\Quest;
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

    public static function byQuest(Quest $quest): self
    {
        return new self($quest->getId());
    }
}
