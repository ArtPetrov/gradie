<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Model\Cpanel\UseCase\Login;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{

    /**
     * @Route("/login",name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function auth(AuthenticationUtils $authenticationUtils): Response
    {
        $form = $this->createForm(Login\Form::class);

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('cpanel/auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/logout",name="logout")
     * @return Response
     * @throws \Exception
     */
    public function logout(): Response
    {
        throw new \Exception('will be intercepted before');
    }


}
