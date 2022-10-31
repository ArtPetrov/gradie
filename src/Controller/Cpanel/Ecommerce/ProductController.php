<?php

declare(strict_types=1);

namespace App\Controller\Cpanel\Ecommerce;

use App\Controller\ErrorHandler;
use App\Model\Ecommerce\Entity\Product\Product;
use App\Model\Ecommerce\ReadModel\Product\ProductFetcher;
use App\Model\Ecommerce\UseCase\Product\Create;
use App\Model\Ecommerce\UseCase\Product\Delete;
use App\Model\Ecommerce\UseCase\Product\Search;
use App\Model\Ecommerce\UseCase\Product\Edit;
use App\Model\Ecommerce\UseCase\Product\Move;
use App\Model\Ecommerce\UseCase\Product\Image;
use App\Model\Review\UseCase\Create\Administrator;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private const PER_PAGE = 15;

    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/products", name="products")
     */
    public function list(Request $request, ProductFetcher $products)
    {
        $query = new Search\Command();
        $form = $this->createForm(Search\Form::class, $query);
        $form->handleRequest($request);

        return $this->render('ecommerce/product/list.html.twig', [
            'products' => $products->findByArticleOrName($query, $request->query->getInt('page', 1), self::PER_PAGE),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/product/create", name="product.create")
     */
    public function create(Request $request, Create\Handler $handler)
    {
        $command = new Create\Command();
        $form = $this->createForm(Create\Form::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'product.created');
                return $this->redirectToRoute('ecommerce.products');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('ecommerce/product/create.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/product/{id}/delete", name="product.delete")
     */
    public function delete(Product $product, Request $request, Delete\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        try {
            $handler->handle(new Delete\Command($product->getId()));
            return $this->json(null, 204);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
        }
        return $this->json(null, 200);
    }

    /**
     * @Route("/product/{id}/position", name="product.position")
     */
    public function position(Product $product, Request $request, Move\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new Move\Command($product, $request->request->get('direction'));
        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            return $this->json(null, 204);
        }
        return $this->json(null, 200);
    }

    /**
     * @Route("/product/{id}", name="product")
     */
    public function product(Product $product, Request $request, Edit\Handler $handler)
    {
        $command = Edit\Command::fromProduct($product);
        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'product.updated');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('ecommerce/product/view.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'form_review' =>$this->createForm(Administrator\Form::class, Administrator\Command::fromProduct($product))->createView(),
        ]);
    }

    /**
     * @Route("/products/recomended/json", name="products.recomended.json")
     */
    public function recomended(Request $request, ProductFetcher $products)
    {
        $querySearch = $request->request->get('query', '');
        return $this->json($products->findForRecommended($querySearch));
    }

    /**
     * @Route("/products/composition/json", name="products.composition.json")
     */
    public function composition(Request $request, ProductFetcher $products)
    {
        $querySearch = $request->request->get('query', '');
        return $this->json($products->findForComposition($querySearch));
    }

    /**
     * @Route("/products/images/upload", name="products.image.upload", methods={"POST","PUT"})
     */
    public function upload(Request $request, Image\Upload\Handler $handler, FilterService $filterImages): Response
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new  Image\Upload\Command($request->files->get('file'));
        try {
            $file = $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            return $this->json($e->getMessage(), 400);
        }

        return $this->json([
            'id' => $file->getId(),
            'src' => $filterImages->getUrlOfFilteredImage($file->getPath(), 'product_200_200')],
            201, []);
    }

}
