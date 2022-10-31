<?php

declare(strict_types=1);

namespace App\Model\Quiz\UseCase\Value\Add;

use App\Model\Quiz\Entity\Quest;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public $quest;

    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255)
     */
    public $name;

    /**
     * @Assert\Length(max=255)
     */
    public $value;

    public $cover;

    public $style;

    public function __construct(Quest $quest)
    {
        $this->quest = $quest->getId();
    }
}
