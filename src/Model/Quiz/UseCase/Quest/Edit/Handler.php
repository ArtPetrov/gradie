<?php

declare(strict_types=1);

namespace App\Model\Quiz\UseCase\Quest\Edit;

use App\Model\Quiz\Repository\QuestionRepository;
use App\Model\Flusher;

class Handler
{
    private $flusher;
    private $questions;

    public function __construct(Flusher $flusher, QuestionRepository $questions)
    {
        $this->flusher = $flusher;
        $this->questions = $questions;
    }

    public function handle(Command $command): void
    {
        $quest = $this->questions->getById((int)$command->id);
        $quest
            ->changeSkip((bool)$command->skip)
            ->changeAnotherAnswer((bool)$command->anotherAnswer)
            ->changeHelp($command->help)
            ->changeQuest($command->quest)
            ->rename($command->name);

        $this->flusher->flush($quest);
    }
}
