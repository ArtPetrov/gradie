<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;
use App\Model\Page\Entity\Page;
use App\Model\Page\UseCase\Create;
use App\Model\Page\UseCase\Edit;
use App\Model\Page\UseCase\Remove;
use App\Model\Page\Repository\PageRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{

    private const PER_PAGE = 15;
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/pages", name="pages")
     */
    public function list(Request $request, PageRepository $pages, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $pages->getAll(),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );
        return $this->render('cpanel/page/list.html.twig', [
            'pages' => $pagination
        ]);
    }

    /**
     * @Route("/page/add", name="page.add")
     */
    public function create(Request $request, Create\Handler $handler)
    {
        $command = new Create\Command();
        $form = $this->createForm(Create\Form::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'page.created');
                return $this->redirectToRoute('cpanel.pages');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/page/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/page/{id}", name="page", methods={"POST","GET"})
     */
    public function edit(Page $page, Request $request, Edit\Handler $handler)
    {
        $command = new Edit\Command($page);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'page.updated');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/page/edit.html.twig', [
            'form' => $form->createView(),
            'page'=> $page,
        ]);
    }

    /**
     * @Route("/page/{id}/delete", name="page.delete")
     */
    public function delete(Page $page, Request $request, Remove\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new Remove\Command($page->getId());
        $handler->handle($command);
        return $this->json(null, 204);
    }

}
