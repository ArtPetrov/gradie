<?php

declare(strict_types=1);

namespace App\Controller\Buyer;

use App\Controller\ErrorHandler;
use App\Model\Buyer\UseCase\Login;
use App\Model\Buyer\UseCase\Reset;
use App\Model\Buyer\UseCase\SignUp;
use App\Model\Buyer\Repository\BuyerRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/login",name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function auth(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')
            || $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')
        ) {
            return $this->redirectToRoute('buyer.index');
        }

        $form = $this->get('form.factory')->createNamed('auth_popup', Login\Form::class);

        if ($error = $authenticationUtils->getLastAuthenticationError()) {
            $this->addFlash('error', $error->getMessageKey());
        }

        return $this->render('frontend/buyer/auth/login.html.twig', [
            'last_username' =>  $authenticationUtils->getLastUsername(),
            'auth' => $form->createView(),
        ]);
    }

    /**
     * @Route("/logout",name="logout")
     * @return Response
     * @throws \Exception
     */
    public function logout(): Response
    {
        throw new \Exception('You don\'t see it!');
    }

    /**
     * @Route("/registration",name="signup")
     * @return Response
     */
    public function sigup(Request $request, SignUp\Request\Handler $handler): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')
        || $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')
        ) {
            return $this->redirectToRoute('buyer.index');
        };

        $command = new SignUp\Request\Command();

        $form = $this->createForm(SignUp\Request\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'buyer.success.signup');
                return $this->redirectToRoute('buyer.index');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('frontend/buyer/auth/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reset", name="reset")
     */
    public function request(Request $request, Reset\Request\Handler $handler): Response
    {
        $command = new Reset\Request\Command();

        $form = $this->createForm(Reset\Request\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'reset.send.mail');
                return $this->redirectToRoute('buyer.login');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('frontend/buyer/auth/reset_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reset/{token}", name="reset.password")
     */
    public function reset(string $token, Request $request, BuyerRepository $buyers, Reset\Reset\Handler $handler): Response
    {
        if (!$buyers->existsByResetToken($token)) {
            $this->addFlash('error', 'reset.token.invalid');
            return $this->redirectToRoute('buyer.reset');
        }

        $command = new Reset\Reset\Command($token);

        $form = $this->createForm(Reset\Reset\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'reset.complete');
                return $this->redirectToRoute('buyer.login');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('frontend/buyer/auth/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
