<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;
use App\Model\Review\Entity\Review;
use App\Model\Review\Repository\ReviewRepository;
use App\Model\Review\UseCase\Moderation\Active;
use App\Model\Review\UseCase\Remove;
use App\Model\Review\UseCase\Edit;
use App\Model\Review\UseCase\Create\Administrator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends AbstractController
{
    private const PER_PAGE = 15;
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/review/create", name="review.create")
     */
    public function create(Request $request, Administrator\Handler $handler)
    {
        $command = new Administrator\Command();
        $form = $this->createForm(Administrator\Form::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'review.append');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->redirectToRoute('cpanel.product', ['id' => $command->product]);
    }

    /**
     * @Route("/reviews", name="reviews")
     */
    public function list(Request $request, ReviewRepository $reviews, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $reviews->getAll(),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('cpanel/review/list.html.twig', [
            'reviews' => $pagination
        ]);
    }

    /**
     * @Route("/review/{id}", name="review", methods={"POST","GET"})
     */
    public function edit(Review $review, Request $request, Edit\Administrator\Handler $handler)
    {
        $command = Edit\Administrator\Command::create($review);
        $form = $this->createForm(Edit\Administrator\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'review.updated');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/review/edit.html.twig', [
            'form' => $form->createView(),
            'review'=> $review,
        ]);
    }

    /**
     * @Route("/review/{id}/active/products", name="review.active.product")
     */
    public function activeFromProducts(Review $review, Active\Handler $handler)
    {
        $handler->handle(new Active\Command($review));
        return $this->redirectToRoute('cpanel.product', ['id' => $review->getProduct()->getId()]);
    }

    /**
     * @Route("/review/{id}/active", name="review.active")
     */
    public function active(Review $review, Active\Handler $handler)
    {
        $handler->handle(new Active\Command($review));
        return $this->redirectToRoute('cpanel.reviews');
    }

    /**
     * @Route("/review/{id}/remove", name="review.remove")
     */
    public function remove(Review $review, Request $request, Remove\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new Remove\Command($review->getId());
        $handler->handle($command);
        return $this->json(null, 204);
    }
}
