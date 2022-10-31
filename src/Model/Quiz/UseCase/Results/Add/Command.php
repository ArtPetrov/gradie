<?php

declare(strict_types=1);

namespace App\Model\Quiz\UseCase\Results\Add;

use App\Model\Quiz\Entity\Quiz;
use Symfony\Component\HttpFoundation\Request;

class Command
{
    public $quiz;
    public $name;
    public $email;
    public $phone;
    public $answers;

    public function __construct(Quiz $quiz)
    {
        $this->quiz = $quiz->getId();
    }

    public static function fromRequest(Quiz $quiz, Request $request): self
    {
        $command = new self($quiz);
        $command->answers = $request->request->get('answers');
        $command->name = $request->request->get('name');
        $command->phone = $request->request->get('phone');
        $command->email = $request->request->get('email');
        return $command;
    }
}
