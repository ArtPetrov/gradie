<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Quiz\Entity;
use App\Model\Quiz\UseCase\Quiz;
use App\Model\Quiz\UseCase\Quest;
use App\Model\Quiz\UseCase\Results\Add\Command;
use App\Model\Quiz\UseCase\Results\Add\Handler;
use App\Model\Quiz\UseCase\Value;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class QuizController extends AbstractController
{
    /**
     * @var ErrorHandler
     */
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/quiz/{id}", name="quiz")
     */
    public function quiz(Entity\Quiz $quiz)
    {
        if (!$quiz->isEnable()) {
            return $this->redirectToRoute('error.404');
        }

        return $this->render('frontend/quiz/quiz.html.twig', [
            'quiz' => $quiz,
            'content' => $quiz->getContent()
        ]);
    }

    /**
     * @Route("/quiz/{id}/source", name="quiz.data")
     */
    public function sourceQuiz(Entity\Quiz $quiz, FilterService $filter)
    {
        return $this->json([
            'questions' => $quiz->getQuestsSource($filter),
            'map' => $quiz->getMap()
        ]);
    }

    /**
     * @Route("/quiz/{id}/save", name="quiz.save")
     */
    public function saveResult(Entity\Quiz $quiz, Request $request, Handler $handler)
    {
        $command = Command::fromRequest($quiz, $request);
        $handler->handle($command);
        return $this->json(['status' => 'saved']);
    }
}
