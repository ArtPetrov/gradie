<?php

declare(strict_types=1);

namespace App\Controller\Buyer;

use App\Controller\ErrorHandler;
use App\Model\Buyer\UseCase\Setting\Password;
use App\Model\Buyer\UseCase\Setting\Information;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_BUYER")
 */
class SettingsController extends AbstractController
{
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/settings",name="settings")
     */
    public function settings()
    {
        return $this->render('frontend/buyer/cabinet/settings/main.html.twig');
    }

    /**
     * @Route("/setting/password",name="setting.password")
     */
    public function password(Request $request, Password\Handler $handler): Response
    {
        $command = new Password\Command($this->getUser()->getId());

        $form = $this->createForm(Password\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'reset.complete');
                return $this->redirectToRoute('buyer.settings');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('frontend/buyer/cabinet/settings/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/setting/information",name="setting.information")
     */
    public function information(Request $request, Information\Handler $handler): Response
    {
        $command = Information\Command::fromBuyer($this->getUser());

        $form = $this->createForm(Information\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'information.updated');
                return $this->redirectToRoute('buyer.settings');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('frontend/buyer/cabinet/settings/information.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
