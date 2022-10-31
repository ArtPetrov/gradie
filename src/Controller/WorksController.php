<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Page\Repository\PageRepository;
use App\Model\Works\Entity\Work;
use App\Model\Works\ReadModel\WorkFetcher;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class WorksController extends AbstractController
{
    /**
     * @Route("/work/{id}", name="work")
     */
    public function article(Work $work, Environment $twig)
    {
        try {

            $content = $work->getContent()->getContent();

            if (false !== mb_strripos($content, '#SLIDER_DIY#')) {
                $sliderDiy = $twig->render('widget/frontend/slider_works.html.twig', [
                    'images' => $work->getDiys(),
                    'tag' => 'photo-gallery'
                ]);
                $content = str_replace('#SLIDER_DIY#', $sliderDiy, $content);
            }

            return $this->render('frontend/works/work.html.twig', [
                'work' => $work,
                'content' => $content
            ]);
        } catch (\Exception $e) {
            return $this->redirectToRoute('error.404');
        }
    }

    /**
     * @Route("/works",name="works")
     */
    public function list(WorkFetcher $works, PageRepository $pages)
    {
        $page = $pages->getBySlug('works');
        $results = $works->getLast(6);
        return $this->render('frontend/works/list.html.twig', [
            'header' => $page->getContent()->getHeader(),
            'content' => $page->getContent()->getBody(),
            'seo' => $page->getSeo(),
            'works' => $results,
        ]);
    }

    /**
     * @Route("/works/json",name="works.json")
     */
    public function listJson(Request $request, WorkFetcher $works, FilterService $filter)
    {
        $offset = $request->request->getInt('offset', 0);
        $results = $works->getLast(3 + 1, $offset);
        foreach ($results as $index => $work) {
            if ($work->cover) {
                $results[$index]->cover = $filter->getUrlOfFilteredImage($work->cover, 'work_297_297');
            }
            $results[$index]->link = $this->generateUrl('work', ['id' => (int)$work->id]);
        }
        return $this->json($results);
    }
}
