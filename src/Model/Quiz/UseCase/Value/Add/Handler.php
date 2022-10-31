<?php

declare(strict_types=1);

namespace App\Model\Quiz\UseCase\Value\Add;

use App\Model\File\Service\Uploader;
use App\Model\Quiz\Entity\QuestValue;
use App\Model\Quiz\Repository\QuestionRepository;
use App\Model\Flusher;
use App\Model\Quiz\Repository\ValueRepository;

class Handler
{
    private $flusher;
    private $questions;
    private $values;
    private $uploader;

    public function __construct(
        Flusher $flusher,
        QuestionRepository $questions,
        ValueRepository $values,
        Uploader $uploader
    )
    {
        $this->flusher = $flusher;
        $this->questions = $questions;
        $this->values = $values;
        $this->uploader = $uploader;
    }

    public function handle(Command $command): void
    {
        $quest = $this->questions->getById((int)$command->quest);

        if ($quest->getType()->isMedia()) {

            if (!$command->cover) {
                throw new \DomainException('quiz.cover.not.upload');
            }

            $file = $this->uploader->upload($command->cover, QuestValue::DIRECTORY_FILES, true);
            $value = QuestValue::createMedia(
                $quest,
                $file,
                $command->value,
                $command->name,
                $command->style
            );

        } else {
            $value = QuestValue::create(
                $quest,
                $command->value,
                $command->name,
                $command->style
            );
        }
        $this->values->add($value);
        $this->flusher->flush($value);
    }
}
