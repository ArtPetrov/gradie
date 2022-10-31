<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;
use App\Model\Works\Entity\Work;
use App\Model\Works\Repository\WorkRepository;
use App\Model\Works\UseCase\Create;
use App\Model\Works\UseCase\Edit;
use App\Model\Works\UseCase\Image;
use App\Model\Works\UseCase\Move;
use App\Model\Works\UseCase\Remove;
use Knp\Component\Pager\PaginatorInterface;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WorksController extends AbstractController
{
    private const PER_PAGE = 15;
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/works", name="works")
     */
    public function list(Request $request, WorkRepository $works, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $works->getAll(),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );
        return $this->render('cpanel/works/list.html.twig', [
            'works' => $pagination
        ]);
    }

    /**
     * @Route("/work/create", name="work.create")
     */
    public function create(Request $request, Create\Handler $handler)
    {
        $command = new Create\Command();
        $form = $this->createForm(Create\Form::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'work.created');
                return $this->redirectToRoute('cpanel.works');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/works/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/work/{id}", name="work", methods={"POST","GET"})
     */
    public function edit(Work $work, Request $request, Edit\Handler $handler)
    {
        $command = new Edit\Command($work);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'work.updated');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/works/edit.html.twig', [
            'form' => $form->createView(),
            'work' => $work,
        ]);
    }

    /**
     * @Route("/work/{id}/remove", name="work.remove")
     */
    public function delete(Work $work, Request $request, Remove\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new Remove\Command($work->getId());
        $handler->handle($command);
        return $this->json(null, 204);
    }

    /**
     * @Route("/work/{id}/position", name="work.position")
     */
    public function position(Work $work, Request $request, Move\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new Move\Command($work, $request->request->get('direction'));
        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            return $this->json(null, 204);
        }
        return $this->json(null, 200);
    }

    /**
     * @Route("/work/{type}/upload", name="work.image.upload", methods={"POST","PUT"}, requirements={"slug"="(images|diy)"})
     */
    public function upload(string $type, Request $request, Image\Upload\Handler $handler, FilterService $filterImages): Response
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }

        $command = new Image\Upload\Command($request->files->get('file'),$type);

        try {
            $file = $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            return $this->json($e->getMessage(), 400);
        }

        return $this->json([
            'id' => $file->getId(),
            'src' => $filterImages->getUrlOfFilteredImage($file->getPath(), 'work_148_105')],
            201, []);
    }
}
