<?php

declare(strict_types=1);

namespace App\Controller\Buyer;

use App\Controller\ErrorHandler;
use App\Model\Order\Entity\Order;
use App\Model\Order\UseCase\Canceled;
use App\Model\Order\ReadModel\OrderFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_BUYER")
 */
class OrdersController extends AbstractController
{
    private const PER_PAGE = 10;
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/orders",name="orders")
     */
    public function orders(Request $request, OrderFetcher $orders)
    {
        $pagination = $orders->findByTokenForCabinet(
            $this->getUser()->getBasketToken(),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('frontend/buyer/cabinet/orders/list.html.twig', [
            'orders' => $pagination
        ]);
    }

    /**
     * @Route("/order/{uuid}", name="order")
     * @IsGranted("ORDER_OWNER", subject="order")
     */
    public function order(Order $order)
    {
        return $this->render('frontend/buyer/cabinet/orders/order.html.twig', [
            'order' => $order
        ]);
    }

    /**
     * @Route("/order/{uuid}/canceled", name="order.canceled", methods={"POST"})
     * @IsGranted("ORDER_OWNER", subject="order")
     */
    public function canceled(Order $order, Request $request, Canceled\Order\Handler $handler)
    {
        try {
            if (!$this->isCsrfTokenValid('any', $request->request->get('token'))) {
                throw new \DomainException("csrf.token.invalid");
            }
            $command = Canceled\Order\Command::fromClient($order);
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
        }
        return $this->redirectToRoute("buyer.orders");
    }
}
