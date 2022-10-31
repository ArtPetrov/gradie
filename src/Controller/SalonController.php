<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Salon\ReadModel\SalonFetcher;
use App\Model\Page\Repository\PageRepository;
use App\Model\Salon\Service\YandexMapConvertor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SalonController extends AbstractController
{
    /**
     * @Route("/salons/json", name="salons.json")
     */
    public function salons(YandexMapConvertor $salons)
    {
        return $this->json($salons->render());
    }

    /**
     * @Route("/salons",name="salons")
     */
    public function list(PageRepository $pages)
    {
        $page = $pages->getBySlug('salons');

        return $this->render('frontend/pages/salons.html.twig', [
            'header' => $page->getContent()->getHeader(),
            'slug' => $page->getSettings()->getSlug(),
            'content' => $page->getContent()->getBody(),
            'seo' => $page->getSeo()
        ]);
    }
}
