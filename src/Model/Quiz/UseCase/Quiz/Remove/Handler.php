<?php

declare(strict_types=1);

namespace App\Model\Quiz\UseCase\Quiz\Remove;

use App\Model\Flusher;
use App\Model\Quiz\Repository\QuizRepository;

class Handler
{
    private $quizs;
    private $flusher;

    public function __construct(QuizRepository $quizs, Flusher $flusher)
    {
        $this->quizs = $quizs;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $quiz = $this->quizs->getById($command->id);
        $this->quizs->remove($quiz);
        $this->flusher->flush();
    }
}
