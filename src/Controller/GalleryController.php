<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Gallery\Entity\Album;
use App\Model\Gallery\ReadModel\AlbumFetcher;
use App\Model\Page\Repository\PageRepository;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GalleryController extends AbstractController
{
    /**
     * @Route("/gallery/{id}", name="gallery")
     */
    public function article(Album $album)
    {
        try {
            return $this->render('frontend/gallery/album.html.twig', [
                'album' => $album
            ]);
        } catch (\Exception $e) {
            return $this->redirectToRoute('error.404');
        }
    }

    /**
     * @Route("/galleries",name="galleries")
     */
    public function list(AlbumFetcher $gallery, PageRepository $pages, FilterService $filter)
    {
        $page = $pages->getBySlug('galleries');
        $results = $gallery->getLast(12);
        return $this->render('frontend/gallery/list.html.twig', [
            'header' => $page->getContent()->getHeader(),
            'content' => $page->getContent()->getBody(),
            'seo' => $page->getSeo(),
            'gallery' => $results,
        ]);
    }

    /**
     * @Route("/galleries/json",name="galleries.json")
     */
    public function listJson(Request $request, AlbumFetcher $gallery, FilterService $filter)
    {
        $offset = $request->request->getInt('offset', 0);
        $results = $gallery->getLast(4 + 1, $offset);
        foreach ($results as $index => $album) {
            if($album->cover){
                $results[$index]->cover =  $filter->getUrlOfFilteredImage($album->cover, 'gallery_215_215');
            }
            $results[$index]->link = $this->generateUrl('gallery', ['id' => (int) $album->id]);
        }
        return $this->json($results);
    }
}
