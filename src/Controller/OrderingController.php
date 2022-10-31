<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Buyer\Entity\Buyer;
use App\Model\Order\Service\CurrentOrder;
use App\Model\Order\UseCase\Ordering;
use App\Model\Basket\Repository\BasketRepository;
use App\Model\Basket\Service\ReadBasketToken;
use App\Model\Promocode\Repository\PromocodeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderingController extends AbstractController
{
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/ordering/contact",name="ordering.step1")
     */
    public function step1(
        Request $request,
        ReadBasketToken $token,
        BasketRepository $basket,
        Ordering\Contact\Handler $handler,
        CurrentOrder $currentOrder
    ): Response
    {
        if (!$basket->hasItemsForToken($token = $token->getToken())) {
            return $this->redirectToRoute('basket');
        }

        if ($order = $currentOrder->getOrder()) {
            if ($order->getBasket()->getToken() !== $token->getToken()) {
                $request->getSession()->set(CurrentOrder::NAMING, null);
                return $this->redirectToRoute('basket');
            }
            $command = Ordering\Contact\Command::fromOrder($order);
        } elseif ($this->getUser() instanceof Buyer) {
            $command = Ordering\Contact\Command::fromBuyer($this->getUser());
        } else {
            $command = new Ordering\Contact\Command();
        }

        $form = $this->createForm(Ordering\Contact\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('ordering.step2');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('frontend/ordering/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ordering/address",name="ordering.step2")
     */
    public function step2(
        Request $request,
        ReadBasketToken $token,
        BasketRepository $basket,
        Ordering\Address\Handler $handler,
        PromocodeRepository $promocodes,
        CurrentOrder $currentOrder
    ): Response
    {
        if (!$basket->hasItemsForToken($token = $token->getToken()) || !($order = $currentOrder->getOrder())) {
            return $this->redirectToRoute('basket');
        }

        if ($order->getBasket()->getToken() !== $token->getToken()) {
            $request->getSession()->set(CurrentOrder::NAMING, null);
            return $this->redirectToRoute('basket');
        }

        $totalWithPromo = $total = $basket->getTotalPriceForBasket($token);

        if ($order->hasPromocode() && $promo = $order->getPromocode()->getCode()) {
            if ($promocodes->hasCode($promo)) {
                $promocode = $promocodes->getByCode($promo);
                $totalWithPromo -= $promocode->getDiscount($totalWithPromo);
            }
        }

        $command = Ordering\Address\Command::fromOrderWithOrderPrice($order, $total, $totalWithPromo);

        $form = $this->createForm(Ordering\Address\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('ordering.step3');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('frontend/ordering/address.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ordering/manager-support",name="ordering.step3")
     */
    public function step3(
        Request $request,
        ReadBasketToken $token,
        BasketRepository $basket,
        Ordering\ManagerSupport\Handler $handler,
        CurrentOrder $currentOrder
    ): Response
    {
        if (!$basket->hasItemsForToken($token = $token->getToken()) || !($order = $currentOrder->getOrder())) {
            return $this->redirectToRoute('basket');
        }

        if ($order->getBasket()->getToken() !== $token->getToken()) {
            $request->getSession()->set(CurrentOrder::NAMING, null);
            return $this->redirectToRoute('basket');
        }

        $command = Ordering\ManagerSupport\Command::fromOrder($order);

        $form = $this->createForm(Ordering\ManagerSupport\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if ($idOrder = $handler->handle($command)) {
                    return $this->render('frontend/ordering/manager_wait.html.twig', [
                        'orderId' => $idOrder,
                    ]);
                }
                return $this->redirectToRoute('ordering.step4');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('frontend/ordering/manager_support.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ordering/payment",name="ordering.step4")
     */
    public function step4(
        Request $request,
        ReadBasketToken $token,
        BasketRepository $basket,
        Ordering\Payment\Handler $handler,
        CurrentOrder $currentOrder
    ): Response
    {
        if (!$basket->hasItemsForToken($token = $token->getToken()) || !($order = $currentOrder->getOrder())) {
            return $this->redirectToRoute('basket');
        }

        if ($order->getBasket()->getToken() !== $token->getToken()) {
            $request->getSession()->set(CurrentOrder::NAMING, null);
            return $this->redirectToRoute('basket');
        }

        $command = Ordering\Payment\Command::fromOrder($order);

        $form = $this->createForm(Ordering\Payment\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $order = $handler->handle($command);

                if ($order->getPayment()->isOnline()) {
                    return $this->redirectToRoute('payment.order', ['uuid' => $order->getUuid()]);
                }

                return $this->render('frontend/ordering/completed.html.twig', [
                    'orderId' => $order->getId(),
                ]);
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('frontend/ordering/payment.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
