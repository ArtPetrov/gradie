<?php

declare(strict_types=1);

namespace App\Controller\Dealer;

use App\Model\Dealer\Repository\DealerRepository;
use App\Model\Dealer\UseCase\ResetPassword;
use App\Controller\ErrorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResetController extends AbstractController
{
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/reset", name="reset")
     */
    public function request(Request $request, ResetPassword\Request\Handler $handler): Response
    {
        $command = new ResetPassword\Request\Command();

        $form = $this->createForm(ResetPassword\Request\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'reset.send.mail');
                return $this->redirectToRoute('dealer.login');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('dealer/auth/reset_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reset/{token}", name="reset.password")
     */
    public function reset(string $token, Request $request, DealerRepository $dealers, ResetPassword\Reset\Handler $handler): Response
    {
        if (!$dealers->existsByResetToken($token)) {
            $this->addFlash('error', 'reset.token.invalid');
            return $this->redirectToRoute('dealer.reset');
        }

        $command = new ResetPassword\Reset\Command($token);

        $form = $this->createForm(ResetPassword\Reset\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'reset.complete');
                return $this->redirectToRoute('dealer.login');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('dealer/auth/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
