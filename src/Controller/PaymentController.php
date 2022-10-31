<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Order\Entity\Invoice\Invoice;
use App\Model\Order\Entity\Order;
use App\Model\Order\UseCase\Ordering;
use App\Model\Payment\Entity\Payment;
use App\Model\Payment\UseCase\Create;
use App\Model\Payment\UseCase\Moneta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/payment/moneta",name="payment.moneta")
     */
    public function handlerMoneta(Request $request, Moneta\Handler $handler): Response
    {
        try {
            $handler->handle(Moneta\Command::fromRequest($request));
        } catch (\Exception $e) {
            return new Response('FAIL');
        }
        return new Response('SUCCESS');
    }

    /**
     * @Route("/payment/{slug}",name="payment.static",requirements={"slug"="(refuse|fail|success|progress)"})
     */
    public function pages($slug): Response
    {
        return $this->render('frontend/payment/' . $slug . '.html.twig');
    }

    /**
     * @Route("/payment/{uuid}",name="payment")
     */
    public function byPayment(Payment $payment, Request $request): Response
    {
        $command = Create\Command::fromPayment($payment);
        $form = $this->createForm(Create\Form::class, $command, [
            'action' => $this->getParameter('payment_handler'),
            'method' => $this->getParameter('payment_handler_method')
        ]);

        try {
            if (!$payment->getStatus()->isWait()) {
                throw new \DomainException('payment.not.wait');
            }

        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('payment.refuse');
        }

        return $this->render('frontend/payment/pay.html.twig', [
            'form' => $form->createView(),
            'payment' => $payment
        ]);
    }

    /**
     * @Route("/payment/order/{uuid}",name="payment.order")
     */
    public function byOrder(Order $order, Request $request, Create\ByOrder\Handler $handler): Response
    {
        try {
            $payment = $handler->handle(Create\ByOrder\Command::fromOrder($order));
            return $this->byPayment($payment, $request);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('payment.refuse');
    }

    /**
     * @Route("/payment/invoice/{id}",name="payment.invoice")
     */
    public function byInvoice(Invoice $invoice, Request $request, Create\ByInvoice\Handler $handler): Response
    {
        try {
            $payment = $handler->handle(Create\ByInvoice\Command::fromInvoice($invoice));
            return $this->byPayment($payment, $request);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('payment.refuse');
    }
}
