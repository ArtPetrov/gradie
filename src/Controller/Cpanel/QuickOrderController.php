<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;
use App\Model\QuickOrder\UseCase\Remove;
use App\Model\QuickOrder\UseCase\Edit;
use App\Model\QuickOrder\Entity\Order;
use App\Model\QuickOrder\Repository\QuickOrderRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class QuickOrderController extends AbstractController
{
    private const PER_PAGE = 15;
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/quick-orders", name="quick.orders")
     */
    public function list(Request $request, QuickOrderRepository $orders, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $orders->getAll(),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('cpanel/quick_order/list.html.twig', [
            'orders' => $pagination
        ]);
    }

    /**
     * @Route("/quick-order/{id}", name="quick.order", methods={"POST","GET"})
     */
    public function edit(Order $order, Request $request, Edit\Handler $handler)
    {
        $command = Edit\Command::fromAdministrator($order);
        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'quick.order.update');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->render('cpanel/quick_order/edit.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
        ]);
    }

    /**
     * @Route("/quick-order/{id}/remove", name="quick.order.remove")
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
}

