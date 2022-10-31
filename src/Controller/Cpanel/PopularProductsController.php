<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;
use App\Model\PopularProducts\Entity\PopularProducts;
use App\Model\PopularProducts\ReadModel\PopularFetcher;
use App\Model\PopularProducts\UseCase\Move;
use App\Model\PopularProducts\UseCase\Edit;
use App\Model\PopularProducts\UseCase\Create;
use App\Model\PopularProducts\UseCase\Remove;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PopularProductsController extends AbstractController
{
    private const PER_PAGE = 15;
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/popular-products", name="popular.products")
     */
    public function list(Request $request, PopularFetcher $populars, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $populars->getAll(),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );
        return $this->render('cpanel/popular_products/list.html.twig', [
            'populars' => $pagination
        ]);
    }

    /**
     * @Route("/popular-products/create", name="popular.product.create")
     */
    public function create(Request $request, Create\Handler $handler)
    {
        $command = new Create\Command();
        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'popular.product.created');
                return $this->redirectToRoute('cpanel.popular.products');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->render('cpanel/popular_products/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/popular-product/{id}", name="popular.product", methods={"POST","GET"})
     */
    public function edit(PopularProducts $popular, Request $request, Edit\Handler $handler)
    {
        $command = new Edit\Command($popular);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'popular.product.updated');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/popular_products/edit.html.twig', [
            'form' => $form->createView(),
            'popular'=> $popular
        ]);
    }

    /**
     * @Route("/popular-product/{id}/remove", name="popular.product.remove")
     */
    public function delete(PopularProducts $popular, Request $request, Remove\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new Remove\Command($popular->getId());
        $handler->handle($command);
        return $this->json(null, 204);
    }

    /**
     * @Route("/popular-product/{id}/position", name="popular.product.position")
     */
    public function position(PopularProducts $popular, Request $request, Move\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new Move\Command($popular->getId(), $request->request->get('direction'));
        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            return $this->json(null, 204);
        }
        return $this->json(null, 200);
    }

}
