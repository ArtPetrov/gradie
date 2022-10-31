<?php

declare(strict_types=1);

namespace App\Model\Quiz\UseCase\Quest\Edit;

use App\Model\Quiz\Entity\Quest;
use App\Model\Quiz\Entity\Quiz;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public $quiz;
    public $id;

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

    public static function fromQuest(Quest $quest): self
    {
        $command = new self($quest->getQuiz());
        $command->id = $quest->getId();
        $command->name = $quest->getName();
        $command->help = $quest->getHelp();
        $command->quest = $quest->getQuest();
        $command->type = $quest->getType()->getType();
        $command->anotherAnswer = $quest->supportAnotherAnswer();
        $command->skip = $quest->isSkip();
        return $command;
    }
}
