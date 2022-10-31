<?php

declare(strict_types=1);

namespace App\Model\Quiz\UseCase\Quiz\Create;

use App\Model\File\Service\Uploader;
use App\Model\Quiz\Entity\Quiz;
use App\Model\Quiz\Repository\QuizRepository;
use App\Model\Flusher;

class Handler
{
    private $flusher;
    private $quizs;

    /**
     * @var Uploader
     */
    private $uploader;

    public function __construct(Flusher $flusher, QuizRepository $quizs,  Uploader $uploader)
    {
        $this->flusher = $flusher;
        $this->quizs = $quizs;
        $this->uploader = $uploader;
    }

    public function handle(Command $command): void
    {
        $quiz = Quiz::create(
            $command->name,
            $command->enable,
            $command->textBegin,
            $command->textEnd,
            $command->content
        );

        if ($command->cover) {
            $file = $this->uploader->upload($command->cover, Quiz::DIRECTORY_FILES, true);
            $quiz->uploadCover($file);
        }

        $this->quizs->add($quiz);
        $this->flusher->flush($quiz);
    }
}
