<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;
use App\Model\Ecommerce\ReadModel\Product\ProductFetcher;
use App\Model\Order\Entity\Order;
use App\Model\Order\UseCase\Basket;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BasketController extends AbstractController
{
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/order/{id}/basket", methods={"DELETE"})
     */
    public function removeProduct(Order $order, Request $request, Basket\Remove\Handler $handler)
    {
        $product = $request->request->getInt('id');
        try {
            $handler->handle(Basket\Remove\Command::fromOrder($order, $product));
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 406);
        }
        return $this->json([], 204);
    }

    /**
     * @Route("/order/{id}/basket", methods={"POST"})
     */
    public function addProduct(Order $order, Request $request, Basket\Add\Handler $handler)
    {
        $product = $request->request->getInt('id');
        try {
            $handler->handle(Basket\Add\Command::fromOrder($order, $product));
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 406);
        }
        return $this->json([], 200);
    }

    /**
     * @Route("/order/{id}/basket", name="order.basket", methods={"PUT"})
     */
    public function updateCountProduct(Order $order, Request $request, Basket\Update\Handler $handler)
    {
        $product = $request->request->getInt('id');
        $count = $request->request->getInt('count');
        try {
            $handler->handle(Basket\Update\Command::fromOrder($order, $product, $count));
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 406);
        }
        return $this->json([], 200);
    }

    /**
     * @Route("/order/{id}/basket", name="order.product.search", methods={"GET"})
     */
    public function searchProduct(Request $request, ProductFetcher $products)
    {
        $querySearch = $request->query->get('query', '');
        return $this->json($products->searchForBasket($querySearch));
    }
}
