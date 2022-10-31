<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Ecommerce\ReadModel\Product\ProductFetcher;
use App\Model\Page\Repository\PageRepository;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/search",name="search")
     */
    public function search(Request $request, ProductFetcher $products,  PageRepository $pages)
    {

        $query = trim($request->query->get('q', ''));
        $page = $pages->getBySlug('search');
        $results = $products->searchForQuery($query);

        return $this->render('frontend/search/result.html.twig', [
            'header' => $page->getContent()->getHeader(),
            'content' => $page->getContent()->getBody(),
            'query' => $query,
            'seo' => $page->getSeo(),
            'products' => $results,
        ]);
    }


    /**
     * @Route("/search/json",name="search.json")
     */
    public function searchJson(Request $request, ProductFetcher $products, FilterService $filter)
    {
        $query = $request->request->get('query', '');
        $results = $products->searchForQuery($query);
        foreach ($results as $index => $product) {
            $results[$index]->cost = number_format((float)$product->cost, 2, '.', ' ');
            $results[$index]->link = $this->generateUrl('product', ['id' => (int) $product->id]);
            $results[$index]->cover = $product->cover ?
                $filter->getUrlOfFilteredImage($product->cover, 'product_48')
                : 'https://imgholder.ru/48/EEE/';
        }

        return $this->json($results);
    }
}