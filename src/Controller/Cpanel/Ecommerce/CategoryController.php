<?php

declare(strict_types=1);

namespace App\Controller\Cpanel\Ecommerce;

use App\Controller\ErrorHandler;
use App\Model\Ecommerce\Entity\Category\Category;
use App\Model\Ecommerce\Repository\CategoryRepository;
use App\Model\Ecommerce\UseCase\Category\Create;
use App\Model\Ecommerce\UseCase\Category\Edit;
use App\Model\Ecommerce\UseCase\Category\Filter\Rename\Command;
use App\Model\Ecommerce\UseCase\Category\Filter\Rename\Handler;
use App\Model\Ecommerce\UseCase\Category\Move;
use App\Model\Ecommerce\UseCase\Category\Delete;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/categories", name="categories")
     */
    public function list(CategoryRepository $categories)
    {
        return $this->render('ecommerce/category/list.html.twig', [
            'categories' => $categories->getRepository()->getRootNodes('position', 'asc'),
            'repository' => $categories->getRepository(),
        ]);
    }

    /**
     * @Route("/category/create", name="category.create")
     */
    public function create(Request $request, Create\Handler $handler)
    {
        $command = new Create\Command();
        $form = $this->createForm(Create\Form::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'category.created');
                return $this->redirectToRoute('ecommerce.categories');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('ecommerce/category/create.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/category/{id}/delete", name="category.delete")
     */
    public function delete(Category $category, Request $request, Delete\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        try {
            $handler->handle(new Delete\Command($category->getId()));
            return $this->json(null, 204);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
        }
        return $this->json(null, 200);
    }


    /**
     * @Route("/category/{id}", name="category")
     */
    public function attribute(Category $category, Request $request, Edit\Handler $handler)
    {
        $command = Edit\Command::fromCategory($category);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'category.updated');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('ecommerce/category/view.html.twig', [
            'attribute' => $category,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/category/{id}/position", name="category.position", methods={"POST","GET"})
     */
    public function position(Category $category, Request $request, Move\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new Move\Command($category, $request->request->get('direction'));
        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            return $this->json(null, 204);
        }
        return $this->json(null, 200);
    }

    /**
     * @Route("/category-test", name="category.test")
     */
    public function testMe(Handler $handler)
    {
        $handler->handle(new Command('tsvet','tsvet-new'));
        return $this->json(null);
    }


}
