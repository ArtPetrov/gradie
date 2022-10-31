<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;
use App\Model\Gallery\Entity\Album;
use App\Model\Gallery\Repository\AlbumRepository;
use App\Model\Gallery\UseCase\Create;
use App\Model\Gallery\UseCase\Edit;
use App\Model\Gallery\UseCase\Image;
use App\Model\Gallery\UseCase\Move;
use App\Model\Gallery\UseCase\Remove;
use Knp\Component\Pager\PaginatorInterface;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GalleryController extends AbstractController
{
    private const PER_PAGE = 15;
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/gallery", name="gallery")
     */
    public function list(Request $request, AlbumRepository $gallery, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $gallery->getAll(),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );
        return $this->render('cpanel/gallery/list.html.twig', [
            'gallery' => $pagination
        ]);
    }

    /**
     * @Route("/album/create", name="album.create")
     */
    public function create(Request $request, Create\Handler $handler)
    {
        $command = new Create\Command();
        $form = $this->createForm(Create\Form::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'album.created');
                return $this->redirectToRoute('cpanel.gallery');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/gallery/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/album/{id}", name="album", methods={"POST","GET"})
     */
    public function edit(Album $album, Request $request, Edit\Handler $handler)
    {
        $command = new Edit\Command($album);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'album.updated');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/gallery/edit.html.twig', [
            'form' => $form->createView(),
            'album'=> $album,
        ]);
    }

    /**
     * @Route("/album/{id}/remove", name="album.remove")
     */
    public function delete(Album $album, Request $request, Remove\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new Remove\Command($album->getId());
        $handler->handle($command);
        return $this->json(null, 204);
    }

    /**
     * @Route("/album/{id}/position", name="album.position")
     */
    public function position(Album $album, Request $request, Move\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new Move\Command($album, $request->request->get('direction'));
        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            return $this->json(null, 204);
        }
        return $this->json(null, 200);
    }

    /**
     * @Route("/gallery/images/upload", name="gallery.image.upload", methods={"POST","PUT"})
     */
    public function upload(Request $request, Image\Upload\Handler $handler, FilterService $filterImages): Response
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new  Image\Upload\Command($request->files->get('file'));
        try {
            $file = $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            return $this->json($e->getMessage(), 400);
        }

        return $this->json([
            'id' => $file->getId(),
            'src' => $filterImages->getUrlOfFilteredImage($file->getPath(), 'gallery_215_215')],
            201, []);
    }
}
