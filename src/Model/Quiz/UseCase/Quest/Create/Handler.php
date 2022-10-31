<?php

declare(strict_types=1);

namespace App\Model\Quiz\UseCase\Quest\Create;

use App\Model\Quiz\Entity\Quest;
use App\Model\Quiz\Entity\QuestType;
use App\Model\Quiz\Repository\QuestionRepository;
use App\Model\Flusher;
use App\Model\Quiz\Repository\QuizRepository;

class Handler
{
    private $flusher;
    private $questions;
    private $quizs;

    public function __construct(Flusher $flusher, QuestionRepository $questions, QuizRepository $quizs)
    {
        $this->flusher = $flusher;
        $this->questions = $questions;
        $this->quizs = $quizs;
    }

    public function handle(Command $command): void
    {
        $quiz = $this->quizs->getById((int)$command->quiz);
        $quest = Quest::create(
            $quiz,
            new QuestType($command->type),
            $command->name,
            $command->quest,
            $command->help,
            $command->skip,
            $command->anotherAnswer
        );

        $this->questions->add($quest);
        $this->flusher->flush($quest);
    }
}
