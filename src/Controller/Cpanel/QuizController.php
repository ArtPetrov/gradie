<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;
use App\Model\Quiz\Entity;
use App\Model\Quiz\Repository\QuizRepository;
use App\Model\Quiz\Repository\QuestionRepository;
use App\Model\Quiz\Repository\ResultRepository;
use App\Model\Quiz\Repository\ValueRepository;
use App\Model\Quiz\UseCase\Quiz;
use App\Model\Quiz\UseCase\Quest;
use App\Model\Quiz\UseCase\Value;
use App\Model\Quiz\UseCase\Results;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ROOT")
 */
class QuizController extends AbstractController
{

    private const PER_PAGE = 10;

    /**
     * @var ErrorHandler
     */
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/quiz/{id}/map", name="quiz.map")
     */
    public function viewMap(Entity\Quiz $quiz, QuestionRepository $questions)
    {
        $quests = $questions->getAllForQuiz($quiz);

        return $this->render('cpanel/quiz/map.html.twig', [
            'quiz' => $quiz,
            'questions' => $quests
        ]);
    }

    /**
     * @Route("/quiz/{id}/map/save", name="quiz.map.save")
     */
    public function saveMap(Entity\Quiz $quiz, Request $request, EntityManagerInterface $em)
    {
        $quiz->saveMap($request->request->get('map'));
        $em->flush();
        return $this->json(['status' => 'success']);
    }

    /**
     * @Route("/quizs", name="quizs")
     * @IsGranted("ROLE_ROOT")
     */
    public function list(Request $request, QuizRepository $quizRepository, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $quizRepository->getAll(),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('cpanel/quiz/list.html.twig', [
            'quizs' => $pagination
        ]);
    }

    /**
     * @Route("/quiz/results", name="quiz.results")
     * @IsGranted("ROLE_ROOT")
     */
    public function listResults(Request $request, ResultRepository $results, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $results->getAll(),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('cpanel/quiz/list_result.html.twig', [
            'results' => $pagination
        ]);
    }

    /**
     * @Route("/quiz/result/{id}", name="quiz.result")
     * @IsGranted("ROLE_ROOT")
     */
    public function viewResult(Entity\QuizResult $result, EntityManagerInterface $em)
    {
        $result->changeStatus(new Entity\Status(Entity\Status::VIEW));
        $em->flush();

        return $this->render('cpanel/quiz/one_result.html.twig', [
            'result' => $result,
        ]);
    }

    /**
     * @Route("/quiz/{id}/questions", name="quiz.questions")
     * @IsGranted("ROLE_ROOT")
     */
    public function listQuestions(Entity\Quiz $quiz, Request $request, QuestionRepository $questions, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $questions->getAllForQuiz($quiz),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('cpanel/quiz/list_questions.html.twig', [
            'questions' => $pagination,
            'quiz' => $quiz,
        ]);
    }

    /**
     * @Route("/quiz/question/{id}/values", name="quiz.question.values")
     * @IsGranted("ROLE_ROOT")
     */
    public function listValues(Entity\Quest $quest, Request $request, ValueRepository $values, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $values->getAllForQuest($quest),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('cpanel/quiz/list_values.html.twig', [
            'values' => $pagination,
            'quiz' => $quest->getQuiz(),
            'quest' => $quest,
        ]);
    }

    /**
     * @Route("/quiz/add", name="quiz.add")
     */
    public function createQuiz(Request $request, Quiz\Create\Handler $handler)
    {
        $command = new Quiz\Create\Command();
        $form = $this->createForm(Quiz\Create\Form::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'quiz.created');
                return $this->redirectToRoute('cpanel.quizs');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/quiz/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/quiz/{id}/quest/add", name="quiz.quest.add")
     */
    public function createQuest(Entity\Quiz $quiz, Request $request, Quest\Create\Handler $handler)
    {
        $command = new Quest\Create\Command($quiz);
        $form = $this->createForm(Quest\Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'quiz.quest.created');
                return $this->redirectToRoute('cpanel.quiz.questions', ['id' => $quiz->getId()]);
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/quiz/create_quest.html.twig', [
            'form' => $form->createView(),
            'quiz' => $quiz
        ]);
    }

    /**
     * @Route("/quiz/question/{id}/value/add", name="quiz.quest.value.add")
     */
    public function createValue(Entity\Quest $quest, Request $request, Value\Add\Handler $handler)
    {
        $command = new Value\Add\Command($quest);
        $form = $this->createForm(Value\Add\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'quiz.quest.value.created');
                return $this->redirectToRoute('cpanel.quiz.question.values', ['id' => $quest->getId()]);
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/quiz/add_value.html.twig', [
            'form' => $form->createView(),
            'quiz' => $quest->getQuiz(),
            'quest' => $quest
        ]);
    }

    /**
     * @Route("/quiz/{id}/edit", name="quiz.edit")
     */
    public function editQuiz(Entity\Quiz $quiz, Request $request, Quiz\Edit\Handler $handler)
    {
        $command = Quiz\Edit\Command::byQuiz($quiz);
        $form = $this->createForm(Quiz\Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'quiz.updated');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/quiz/edit.html.twig', [
            'form' => $form->createView(),
            'quiz' => $quiz,
        ]);
    }

    /**
     * @Route("/quiz/question/{id}/edit", name="quiz.question.edit")
     */
    public function editQuest(Entity\Quest $quest, Request $request, Quest\Edit\Handler $handler)
    {
        $command = Quest\Edit\Command::fromQuest($quest);
        $form = $this->createForm(Quest\Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'quiz.quest.updated');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/quiz/edit_quest.html.twig', [
            'form' => $form->createView(),
            'quiz' => $quest->getQuiz(),
            'quest' => $quest,
        ]);
    }

    /**
     * @Route("/quiz/question/value/{id}/edit", name="quiz.question.value.edit")
     */
    public function editValue(Entity\QuestValue $value, Request $request, Value\Edit\Handler $handler)
    {
        $command = Value\Edit\Command::fromQuestValue($value);
        $form = $this->createForm(Value\Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'quiz.quest.value.updated');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/quiz/edit_value.html.twig', [
            'form' => $form->createView(),
            'quiz' => $value->getQuest()->getQuiz(),
            'quest' => $value->getQuest(),
            'value' => $value,
        ]);
    }

    /**
     * @Route("/quiz/{id}/remove", name="quiz.remove")
     */
    public function removeQuiz(Entity\Quiz $quiz, Request $request, Quiz\Remove\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $handler->handle(Quiz\Remove\Command::byQuiz($quiz));
        return $this->json(null, 204);
    }

    /**
     * @Route("/quiz/question/{id}/remove", name="quiz.question.remove")
     */
    public function removeQuest(Entity\Quest $quest, Request $request, Quest\Remove\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $handler->handle(Quest\Remove\Command::byQuest($quest));
        return $this->json(null, 204);
    }

    /**
     * @Route("/quiz/question/value/{id}/remove", name="quiz.question.value.remove")
     */
    public function removeValue(Entity\QuestValue $value, Request $request, Value\Remove\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $handler->handle(Value\Remove\Command::byQuestValue($value));
        return $this->json(null, 204);
    }

    /**
     * @Route("/quiz/result/{id}/remove", name="quiz.result.remove")
     */
    public function removeResult(Entity\QuizResult $result, Request $request, Results\Remove\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $handler->handle(Results\Remove\Command::byQuestValue($result));
        return $this->json(null, 204);
    }
}
