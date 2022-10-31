<?php

declare(strict_types=1);

namespace App\Model\Quiz\UseCase\Quest\Create;

use App\Model\Quiz\Entity\Quiz;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public $quiz;

    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255)
     */
    public $name;

    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255)
     */
    public $quest;

    public $type;
    public $anotherAnswer = false;
    public $help;
    public $skip = true;


    public function __construct(Quiz $quiz)
    {
        $this->quiz = $quiz->getId();
    }
}
