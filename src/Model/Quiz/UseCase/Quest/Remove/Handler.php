<?php

declare(strict_types=1);

namespace App\Model\Quiz\UseCase\Quest\Remove;

use App\Model\Flusher;
use App\Model\Quiz\Repository\QuestionRepository;

class Handler
{
    private $questions;
    private $flusher;

    public function __construct(QuestionRepository $questions, Flusher $flusher)
    {
        $this->questions = $questions;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $quest = $this->questions->getById((int)$command->id);
        $this->questions->remove($quest);
        $this->flusher->flush();
    }
}
