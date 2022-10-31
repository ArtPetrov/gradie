<?php

declare(strict_types=1);

namespace App\Model\Quiz\UseCase\Results\Add;

use App\Model\Flusher;
use App\Model\Quiz\Entity\QuizResult;
use App\Model\Quiz\Repository\QuizRepository;
use App\Model\Quiz\Repository\ResultRepository;

class Handler
{
    private $flusher;
    private $quizs;
    private $results;

    public function __construct(
        Flusher $flusher,
        QuizRepository $quizs,
        ResultRepository $results
    )
    {
        $this->flusher = $flusher;
        $this->quizs = $quizs;
        $this->results = $results;
    }

    public function handle(Command $command): void
    {
        $quiz = $this->quizs->getById((int)$command->quiz);

        $result = QuizResult::create(
            $quiz,
            $command->answers,
            $command->name,
            $command->email,
            $command->phone
        );

        $this->results->add($result);
        $this->flusher->flush($result);
    }
}
