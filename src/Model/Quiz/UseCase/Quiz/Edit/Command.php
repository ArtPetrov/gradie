<?php

declare(strict_types=1);

namespace App\Model\Quiz\UseCase\Quiz\Edit;

use App\Model\Quiz\Entity\Quiz;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
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
    public $textBegin;

    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255)
     */
    public $textEnd;

    public $content;

    public $enable;

    public $cover;
    public $prevCover;

    public static function byQuiz(Quiz $quiz): self
    {
        $command = new self();
        $command->id = $quiz->getId();
        $command->name = $quiz->getName();
        $command->textBegin = $quiz->getTextBegin();
        $command->textEnd = $quiz->getTextEnd();
        $command->content = $quiz->getContent();
        $command->enable = $quiz->isEnable();
        $command->prevCover = $quiz->getCover();
        return $command;
    }
}
