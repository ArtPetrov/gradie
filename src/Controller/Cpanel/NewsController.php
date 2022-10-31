<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;
use App\Model\News\Entity\News;
use App\Model\News\Repository\NewsRepository;
use App\Model\News\UseCase\Create;
use App\Model\News\UseCase\Edit;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ROOT")
 */
class NewsController extends AbstractController
{

    private const PER_PAGE = 5;

    /**
     * @var ErrorHandler
     */
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/news", name="dealer.news")
     * @IsGranted("ROLE_ROOT")
     */
    public function list(Request $request, NewsRepository $newsRepository, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $newsRepository->findSortPublished(),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('cpanel/news/list.html.twig', [
            'news' =>$pagination
        ]);
    }

    /**
     * @Route("/news/add", name="news.add")
     */
    public function createNews(Request $request, Create\Handler $handler)
    {
        $command = new Create\Command();
        $form = $this->createForm(Create\Form::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'news.created');
                return $this->redirectToRoute('cpanel.index');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/news/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/news/{id}", name="news.edit", methods={"POST","GET"})
     */
    public function edit(News $news, Request $request, Edit\Handler $handler)
    {
        $command = new  Edit\Command($news);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'news.updated');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/news/edit.html.twig', [
            'form' => $form->createView(),
            'news'=> $news,
        ]);
    }

    /**
     * @Route("/news/{id}", name="news.delete", methods={"DELETE"})
     */
    public function delete(News $news, Request $request, EntityManagerInterface $em)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $em->remove($news);
        $em->flush();
        return $this->json(null, 204);
    }

}
