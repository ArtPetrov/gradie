<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Dealer\UseCase\Registration;
use App\Model\Page\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class DealerController extends AbstractController
{
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/signup-dealer",name="dealer.signup")
     */
    public function signup(PageRepository $pages,Request $request, Registration\Handler $handler, Registration\Command $command)
    {
        $page = $pages->getBySlug('signup-dealer');

        $signup = false;
        $form = $this->createForm(Registration\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $signup = true;
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        $template = $page->getSettings()->getTemplate() ?? 'static';
        return $this->render('frontend/pages/' . $template . '.html.twig', [
            'header' => $page->getContent()->getHeader(),
            'name' => $page->getName(),
            'content' => $page->getContent()->getBody(),
            'seo' => $page->getSeo(),
            'form' => $form->createView(),
            'complete' => $signup
        ]);

    }
}
