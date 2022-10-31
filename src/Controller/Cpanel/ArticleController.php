<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;
use App\Model\Article\Entity\Article;
use App\Model\Article\Repository\ArticleRepository;
use App\Model\Article\UseCase\Create;
use App\Model\Article\UseCase\Edit;
use App\Model\Article\UseCase\Image;
use App\Model\Article\UseCase\Move;
use App\Model\Article\UseCase\Remove;
use Knp\Component\Pager\PaginatorInterface;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    private const PER_PAGE = 15;
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/articles", name="articles")
     */
    public function list(Request $request, ArticleRepository $articles, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $articles->getAll(),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );
        return $this->render('cpanel/article/list.html.twig', [
            'articles' => $pagination
        ]);
    }

    /**
     * @Route("/article/create", name="article.create")
     */
    public function create(Request $request, Create\Handler $handler)
    {
        $command = new Create\Command();
        $form = $this->createForm(Create\Form::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'article.created');
                return $this->redirectToRoute('cpanel.articles');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/article/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/article/{id}", name="article", methods={"POST","GET"})
     */
    public function edit(Article $article, Request $request, Edit\Handler $handler)
    {
        $command = new Edit\Command($article);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'article.updated');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/article/edit.html.twig', [
            'form' => $form->createView(),
            'article'=> $article,
        ]);
    }

    /**
     * @Route("/article/{id}/remove", name="article.remove")
     */
    public function delete(Article $article, Request $request, Remove\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new Remove\Command($article->getId());
        $handler->handle($command);
        return $this->json(null, 204);
    }

    /**
     * @Route("/article/images/upload", name="article.image.upload", methods={"POST","PUT"})
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
            'src' => $filterImages->getUrlOfFilteredImage($file->getPath(), 'article_215_320')],
            201, []);
    }
}
