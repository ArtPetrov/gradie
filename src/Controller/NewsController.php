<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Article\Entity\Article;
use App\Model\Article\ReadModel\ArticleFetcher;
use App\Model\Page\Repository\PageRepository;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NewsController extends AbstractController
{
    /**
     * @Route("/article/{id}", name="article")
     */
    public function article(Article $article)
    {
        try {
            return $this->render('frontend/news/news.html.twig', [
                'news' => $article
            ]);
        } catch (\Exception $e) {
            return $this->redirectToRoute('error.404');
        }
    }

    /**
     * @Route("/articles",name="articles")
     */
    public function list(ArticleFetcher $articles, PageRepository $pages, FilterService $filter)
    {
        $page = $pages->getBySlug('articles');
        $results = $articles->getLast(12);
        return $this->render('frontend/news/list.html.twig', [
            'header' => $page->getContent()->getHeader(),
            'content' => $page->getContent()->getBody(),
            'seo' => $page->getSeo(),
            'news' => $results,
        ]);
    }

    /**
     * @Route("/articles/json",name="articles.json")
     */
    public function listJson(Request $request, ArticleFetcher $articles, FilterService $filter)
    {
        $offset = $request->request->getInt('offset', 0);
        $results = $articles->getLast(4 + 1, $offset);
        foreach ($results as $index => $news) {
            if($news->cover){
                $results[$index]->cover =  $filter->getUrlOfFilteredImage($news->cover, 'article_215_320');
            }
            $results[$index]->link = $this->generateUrl('article', ['id' => (int) $news->id]);
            $results[$index]->date = (new \DateTimeImmutable($news->date))->format('d.m.Y');
            $results[$index]->datetime = (new \DateTimeImmutable($news->date))->format('Y-m-d');
        }
        return $this->json($results);
    }
}
