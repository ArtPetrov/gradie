<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;
use App\Model\Basket\Repository\BasketRepository;
use App\Model\Cpanel\UseCase\Filter\Orders;
use App\Model\Order\Entity\Invoice\Invoice;
use App\Model\Order\Entity\Order;
use App\Model\Order\ReadModel\OrderFetcher;
use App\Model\Order\UseCase\Canceled;
use App\Model\Order\UseCase\Compile;
use App\Model\Order\UseCase\Create\FromManager;
use App\Model\Order\UseCase\Edit;
use App\Model\Order\UseCase\Remove;
use App\Model\Order\UseCase\Copy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private const PER_PAGE = 15;
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/orders", name="orders")
     */
    public function list(Request $request, OrderFetcher $orders, Orders\Filter $filter)
    {
        $form = $this->createForm(Orders\Form::class, $filter);
        $form->handleRequest($request);

        $pagination = $orders->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('cpanel/order/list.html.twig', [
            'orders' => $pagination,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/order/{id}", name="order", methods={"POST","GET"})
     */
    public function order(Order $order, BasketRepository $basket, Request $request, Edit\Handler $handler)
    {
        $basketItems = [];
        if ($order->getStatus()->inProcessCreating()) {
            $basketItems = $basket->findAllByToken($order->getBasket());
        }

        $command = Edit\Command::fromOrder($order);
        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'order.update');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        $invoice = $this->get('form.factory')->createNamed('invoice_form', FromManager\Form::class, FromManager\Command::fromOrder($order), [
            'action' => $this->generateUrl("cpanel.invoice.create", ['id' => $order->getId()])
        ]);
        $invoice->handleRequest($request);

        return $this->render('cpanel/order/order.html.twig', [
            'order' => $order,
            'basket' => $basketItems,
            'manager' => $form->createView(),
            'invoiceForm' => $invoice->createView()
        ]);
    }

    /**
     * @Route("/order/invoice/create/{id}", name="invoice.create", methods={"POST"})
     */
    public function createInvoice(Order $order, Request $request, BasketRepository $basketRepository, FromManager\Handler $handler)
    {
        $command = FromManager\Command::fromOrder($order);
        $form = $this->get('form.factory')->createNamed('invoice_form', FromManager\Form::class, $command, [
            'action' => $this->generateUrl("cpanel.invoice.create", ['id' => $order->getId()])
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'invoice.create');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->redirectToRoute('cpanel.order', [
            'id' => $order->getId(),
            '_fragment' => 'invoices'
        ]);
    }

    /**
     * @Route("/order/invoice/canceled/{id}", name="invoice.canceled", methods={"POST"})
     */
    public function canceledInvoice(Invoice $invoice, Request $request, Canceled\Invoice\Handler $handler)
    {
        try {
            if (!$this->isCsrfTokenValid('any', $request->request->get('token'))) {
                throw new \DomainException("csrf.token.invalid");
            }
            $command = Canceled\Invoice\Command::byInvoice($invoice);
            $handler->handle($command);
            $this->addFlash('success', 'invoice.canceled');
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('cpanel.order', [
            'id' => $invoice->getOrder()->getId(),
            '_fragment' => 'invoices'
        ]);
    }

    /**
     * @Route("/order/{id}/canceled", name="order.canceled", methods={"POST"})
     */
    public function canceledOrder(Order $order, Request $request, Canceled\Order\Handler $handler)
    {
        try {
            if (!$this->isCsrfTokenValid('any', $request->request->get('token'))) {
                throw new \DomainException("csrf.token.invalid");
            }
            $command = Canceled\Order\Command::byOrder($order);
            $handler->handle($command);
            $this->addFlash('success', 'order.canceled');
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('cpanel.order', ['id' => $order->getId()]);
    }

    /**
     * @Route("/order/{id}/compile", name="order.compile", methods={"POST"})
     */
    public function compileOrder(Order $order, Request $request, Compile\Handler $handler)
    {
        try {
            if (!$this->isCsrfTokenValid('any', $request->request->get('token'))) {
                throw new \DomainException("csrf.token.invalid");
            }
            $command = Compile\Command::byOrder($order);
            $handler->handle($command);
            $this->addFlash('success', 'order.compile');
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('cpanel.order', ['id' => $order->getId()]);
    }

    /**
     * @Route("/order/{id}/remove", name="order.remove")
     */
    public function remove(Order $order, Request $request, Remove\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new Remove\Command($order->getId());
        $handler->handle($command);
        return $this->json(null, 204);
    }

    /**
     * @Route("/order/{id}/copy", name="order.copy")
     */
    public function copy(Order $order, Request $request, Copy\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token'))) {
            return $this->json(null, 401);
        }
        $command = new Copy\Command($order);
        $newOrder = $handler->handle($command);
        $this->addFlash('success', 'order.create.copy');
        return $this->redirectToRoute('cpanel.order', ['id' => $newOrder->getId()]);
    }
}
