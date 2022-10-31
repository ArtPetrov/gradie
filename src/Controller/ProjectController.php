<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\ErrorHandler;
use App\Model\Page\Repository\PageRepository;
use App\Model\DesignProject\UseCase\Create;
use App\Model\DesignProject\UseCase\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/design-project",name="design.project")
     */
    public function type1(PageRepository $pages, Request $request, Create\Type1\Handler $handler, Create\Type1\Command $command)
    {
        $page = $pages->getBySlug('design-project');

        $makeRequest = false;
        $form = $this->createForm(Create\Type1\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $makeRequest = true;
            } catch (\DomainException $e) {
                $this->errors->handle($e);
            }
        }
        return $this->render('frontend/design_project/type1.html.twig', [
            'header' => $page->getContent()->getHeader(),
            'content' => $page->getContent()->getBody(),
            'seo' => $page->getSeo(),
            'form' => $form->createView(),
            'complete' => $makeRequest
        ]);

    }

    /**
     * @Route("/design-project/n",name="design.project.type2")
     */
    public function type2(PageRepository $pages, Request $request, Create\Type2\Handler $handler, Create\Type2\Command $command)
    {
        $page = $pages->getBySlug('design-project');

        $makeRequest = false;
        $form = $this->createForm(Create\Type2\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $makeRequest = true;
            } catch (\DomainException $e) {
                $this->errors->handle($e);
            }
        }
        return $this->render('frontend/design_project/type2.html.twig', [
            'header' => $page->getContent()->getHeader(),
            'content' => $page->getContent()->getBody(),
            'seo' => $page->getSeo(),
            'form' => $form->createView(),
            'complete' => $makeRequest
        ]);

    }

    /**
     * @Route("/design-project/r",name="design.project.type3")
     */
    public function type3(PageRepository $pages, Request $request, Create\Type3\Handler $handler, Create\Type3\Command $command)
    {
        $page = $pages->getBySlug('design-project');

        $makeRequest = false;
        $form = $this->createForm(Create\Type3\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $makeRequest = true;
            } catch (\DomainException $e) {
                $this->errors->handle($e);
            }
        }
        return $this->render('frontend/design_project/type3.html.twig', [
            'header' => $page->getContent()->getHeader(),
            'content' => $page->getContent()->getBody(),
            'seo' => $page->getSeo(),
            'form' => $form->createView(),
            'complete' => $makeRequest
        ]);

    }

    /**
     * @Route("/design-project/customize",name="design.project.customize")
     */
    public function customize(PageRepository $pages, Request $request, Create\Type4\Handler $handler, Create\Type4\Command $command)
    {
        $page = $pages->getBySlug('design-project');

        $makeRequest = false;
        $form = $this->createForm(Create\Type4\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $makeRequest = true;
            } catch (\DomainException $e) {
                $this->errors->handle($e);
            }
        }
        return $this->render('frontend/design_project/type4.html.twig', [
            'header' => $page->getContent()->getHeader(),
            'content' => $page->getContent()->getBody(),
            'seo' => $page->getSeo(),
            'form' => $form->createView(),
            'complete' => $makeRequest
        ]);

    }

    /**
     * @Route("/design-project/file/upload", name="design.project.file.upload", methods={"POST","PUT"})
     */
    public function upload(Request $request, File\Upload\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new  File\Upload\Command($request->files->get('file'));
        try {
            $file = $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            return $this->json($e->getMessage(), 200);
        }

        return $this->json(['id' => $file->getId()], 201, []);
    }
}
