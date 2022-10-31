<?php

declare(strict_types=1);

namespace App\Controller\Dealer;

use App\Controller\ErrorHandler;
use App\Model\Dealer\UseCase\Edit\Password;
use App\Model\Dealer\UseCase\Edit\Profile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_DEALER")
 */
class ProfileController extends AbstractController
{

    /**
     * @var ErrorHandler
     */
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function profile(Request $request, Profile\Handler $handler)
    {
        $command = Profile\Command::fromDealer($this->getUser());

        $form = $this->createForm(Profile\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'profile.edit');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('dealer/profile/view.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profile/password", name="profile.password")
     */
    public function password(Request $request, Password\Handler $handler)
    {
        $command = new Password\Command($this->getUser());

        $form = $this->createForm(Password\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'profile.edit.password');
                return $this->redirectToRoute('dealer.profile');

            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('dealer/profile/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
