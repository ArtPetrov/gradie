<?php

declare(strict_types=1);

namespace App\Twig\Frontend;

use App\Model\Buyer\UseCase\Login;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LoginWidget extends AbstractExtension
{
    private $container;
    private $form;
    private $utils;

    public function __construct(ContainerInterface $container, FormFactoryInterface $form, AuthenticationUtils $utils)
    {
        $this->container = $container;
        $this->form = $form;
        $this->utils = $utils;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('login_popup', [$this, 'login'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    public function login(Environment $twig): string
    {
        $form = $this->form->createNamed('auth_popup', Login\Form::class);

        return $twig->render('widget/frontend/login.html.twig', [
            'last_username' =>  $this->utils->getLastUsername(),
            'auth' => $form->createView(),
        ]);
    }
}
