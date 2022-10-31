<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;
use App\Model\Cpanel\Entity\CategoryDealer;
use App\Model\Cpanel\Repository\CategoryDealerRepository;
use App\Model\Cpanel\UseCase\CategoryDealer\Edit;
use App\Model\Cpanel\UseCase\CategoryDealer\Create;
use App\Model\Cpanel\UseCase\CategoryDealer\MovePosition;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @IsGranted("ROLE_ROOT")
 */
class CategoryDealerController extends AbstractController
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
     * @Route("/categories-dealer", name="categories.dealer")
     */
    public function categories(Request $request, CategoryDealerRepository $categories, PaginatorInterface $paginator, CsrfTokenManagerInterface $csrfToken)
    {
        $pagination = $paginator->paginate(
            $categories->findBy([], ['position' => 'DESC']),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('cpanel/category_dealer/list.html.twig', [
            'pagination' => $pagination,
            'token_csrf' => $csrfToken->getToken('delete'),
        ]);
    }

    /**
     * @Route("/categories-dealer/add", name="categories.dealer.add")
     */
    public function add(Request $request, Create\Handler $handler)
    {
        $command = new Create\Command();

        $form = $this->createForm(Create\Form::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'category.dealer.created');
                return $this->redirectToRoute('cpanel.categories.dealer');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->render('cpanel/category_dealer/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/categories-dealer/{id}", name="category.dealer", methods={"POST","GET"})
     */
    public function edit(CategoryDealer $categoryDealer, Request $request, Edit\Handler $handler)
    {
        $command = Edit\Command::fromCategoryDealer($categoryDealer);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'category.dealer.updated');
                return $this->redirectToRoute('cpanel.categories.dealer');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/category_dealer/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $categoryDealer,
        ]);
    }

    /**
     * @Route("/categories-dealer/{id}", name="category.dealer.delete", methods={"DELETE"})
     */
    public function delete(CategoryDealer $categoryDealer, Request $request, EntityManagerInterface $em)
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $em->remove($categoryDealer);
        $em->flush();
        return $this->json(null, 204);
    }

    /**
     * @Route("/categories-dealer/{id}/position", name="category.dealer.position", methods={"POST","GET"})
     */
    public function position(CategoryDealer $categoryDealer, Request $request, MovePosition\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new MovePosition\Command($categoryDealer, $request->request->get('direction'));
        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            return $this->json(null, 204);
        }
        return $this->json(null, 200);
    }

}
