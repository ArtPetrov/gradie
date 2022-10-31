<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Basket\Entity\Item;
use App\Model\Basket\Repository\BasketRepository;
use App\Model\Basket\Service\ProductTransformer;
use App\Model\Basket\Service\ReadBasketToken;
use App\Model\Basket\UseCase\Add;
use App\Model\Basket\UseCase\Update;
use App\Model\Basket\UseCase\Remove;
use App\Model\Ecommerce\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BasketController extends AbstractController
{
    /**
     * @Route("/basket",name="basket")
     */
    public function basket(ReadBasketToken $token): Response
    {
        return $this->render('frontend/basket/basket.html.twig', [
            'basket_token' => $token->getToken()
        ]);
    }

    /**
     * @Route("/basket/items", name="basket.items", methods="GET")
     * @param Request $request
     * @return Response
     */
    public function getProducts(Request $request, BasketRepository $basket, ProductTransformer $transformer, ReadBasketToken $token): Response
    {
        $products = array_map(static function (Item $item) use ($transformer) {
            return $transformer->transform($item->getProduct(), $item->getCount());
        }, $basket->findAllByToken($token->getToken()));

        return $this->json(['items' => $products], 200);
    }

    /**
     * @Route("/basket/items", methods="POST")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request, Add\Handler $handler, ProductRepository $products, ProductTransformer $transformer, ReadBasketToken $token): Response
    {
        try {
            $product = $request->request->getInt('id', 1);
            $count = $request->request->getInt('count', 1);
            $command = new Add\Command($token->getToken(), $product, $count);
            $handler->handle($command);
            $product = $products->get($product);
        } catch (\DomainException $e) {
            return $this->json([], 422);
        }
        return $this->json(['item' => $transformer->transform($product, $count)], 200);
    }

    /**
     * @Route("/basket/items", methods="PUT")
     * @param Request $request
     * @return Response
     */
    public function update(Request $request, ReadBasketToken $token, Update\Handler $handler): Response
    {
        $count = $request->request->getInt('count', 1);
        $count = 1 > $count ? 1 : $count;

        $id = $request->request->getInt('id', 1);
        try {
            $command = new Update\Command($token->getToken(), $id, $count);
            $handler->handle($command);
        } catch (\DomainException $e) {
            return $this->json([], 422);
        }
        return $this->json([], 200);
    }

    /**
     * @Route("/basket/items", methods="DELETE")
     * @param Request $request
     * @return Response
     */
    public function remove(Request $request, Remove\Handler $handler, ReadBasketToken $token): Response
    {
        $product = $request->request->getInt('id', 1);
        try {
            $command = new Remove\Command($token->getToken(), $product);
            $handler->handle($command);
        } catch (\DomainException $e) {
            return $this->json([], 422);
        }
        return $this->json([], 200);
    }
}
