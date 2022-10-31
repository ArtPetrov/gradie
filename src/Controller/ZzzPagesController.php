<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Page\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class ZzzPagesController extends AbstractController
{
    /**
     * @Route("/",name="index")
     */
    public function index(PageRepository $pages)
    {
        $page = $pages->getBySlug('index');
        return $this->render('frontend/pages/index.html.twig', [
            'seo' => $page->getSeo()
        ]);
    }

    /**
     * @Route("/404",name="error.404")
     */
    public function error404(PageRepository $pages)
    {
        try {
            $page = $pages->getBySlug('404');
            $template = $page->getSettings()->getTemplate() ?? 'static';
            return $this->render('frontend/pages/' . $template . '.html.twig', [
                'header' => $page->getContent()->getHeader(),
                'name' => $page->getName(),
                'content' => $page->getContent()->getBody(),
                'seo' => $page->getSeo()
            ],
                new Response('', $page->getSettings()->getStatusCode())
            );
        } catch (\Exception $e) {
            return $this->json(['bad request' => '404'], 404);
        }
    }

    /**
     * @Route("/{slug}",name="page",requirements={"slug"=".+"})
     */
    public function page($slug, PageRepository $pages)
    {

        if (in_array($slug, ['index', '404'])) {
            return $this->redirectToRoute('error.404');
        }
        try {
            $page = $pages->getBySlug($slug);
            $template = $page->getSettings()->getTemplate() ?? 'static';
            return $this->render('frontend/pages/' . $template . '.html.twig', [
                'header' => $page->getContent()->getHeader(),
                'name' => $page->getName(),
                'content' => $page->getContent()->getBody(),
                'seo' => $page->getSeo(),
                'slug' => $page->getSettings()->getSlug()
            ],
                new Response('', $page->getSettings()->getStatusCode())
            );
        } catch (\Exception $e) {
            return $this->redirectToRoute('error.404');
        }
    }
}