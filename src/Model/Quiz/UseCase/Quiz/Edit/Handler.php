<?php

declare(strict_types=1);

namespace App\Model\Quiz\UseCase\Quiz\Edit;

use App\Model\File\Service\Uploader;
use App\Model\Flusher;
use App\Model\Quiz\Entity\Quiz;
use App\Model\Quiz\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private $flusher;
    private $quizs;
    private $uploader;
    private $em;

    public function __construct(Flusher $flusher, EntityManagerInterface $em, QuizRepository $quizs,  Uploader $uploader)
    {
        $this->flusher = $flusher;
        $this->quizs = $quizs;
        $this->uploader = $uploader;
        $this->em = $em;
    }

    public function handle(Command $command): void
    {
        $quiz = $this->quizs->getById((int)$command->id);
        $quiz
            ->rename($command->name)
            ->editContent($command->content)
            ->changeTextBegin($command->textBegin)
            ->changeTextEnd($command->textEnd);

        if ((bool)$command->enable) {
            $quiz->enable();
        } else {
            $quiz->disable();
        }

        if ($command->cover) {
            $file = $this->uploader->upload($command->cover, Quiz::DIRECTORY_FILES, true);
            $quiz->uploadCover($file);
            if ($command->prevCover) {
                $this->em->remove($command->prevCover);
            }
        }

        $this->flusher->flush($quiz);
    }
}