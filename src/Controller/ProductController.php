<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Ecommerce\Entity\Product\Product;
use App\Model\Ecommerce\Repository\GroupRepository;
use App\Model\Ecommerce\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/product/{id}",name="product")
     */
    public function index(int $id, ProductRepository $products)
    {
        try {
            $product = $products->get($id);
            return $this->render('frontend/product/info.html.twig', [
                'product' => $product
            ]);
        } catch (\Exception $e) {
            return $this->redirectToRoute('error.404');
        }
    }

    /**
     * @Route("/product/{id}/group",name="product.group")
     */
    public function group(Product $product, GroupRepository $groups): Response
    {
        if (!$group = $groups->findByProduct($product)) {
            return $this->json([], 204);
        }

        return $this->json($group->toJson(), 200);
    }
}
