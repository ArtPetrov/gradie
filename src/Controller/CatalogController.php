<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Ecommerce\Entity\Attribute\Field;
use App\Model\Ecommerce\Helper\ArrayCollectionHelper;
use App\Model\Ecommerce\Helper\FiltersParser;
use App\Model\Ecommerce\ReadModel\Category\CategoryFetcher;
use App\Model\Ecommerce\UseCase\Search;
use App\Model\Page\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CatalogController extends AbstractController
{
    /**
     * @Route("/catalog/products",name="catalog.products")
     */
    public function products(Request $request, Search\Handler $handler)
    {
        $command = Search\Command::fromFrontendRequest($request);
        return $this->json($handler->handle($command));
    }

    /**
     * @Route("/catalog",name="catalog")
     */
    public function catalog(CategoryFetcher $categories, PageRepository $pages)
    {
        $page = $pages->getBySlug('catalog');

        return $this->render('frontend/catalog/main.html.twig', [
            'header' => $page->getContent()->getHeader(),
            'content' => $page->getContent()->getBody(),
            'seo' => $page->getSeo(),
            'categories' => $categories->childsCategory()
        ]);
    }

    /**
     * @Route("/catalog{slug}",name="catalog.categorie",requirements={"slug"=".+"})
     */
    public function category(string $slug, Request $request, CategoryFetcher $categories, Search\Handler $handler)
    {
        try {
            $category = $categories->getByPath($slug);

            $filtersWithSorting = ArrayCollectionHelper::sortableByField($category->getFilters(), 'position');
            $filtersSelectedGroupByType = FiltersParser::getFiltersGroupByType($request->query->get('filters', ''));

            $filters['cost'] = [
                'slug' => 'cost',
                'label' => 'Цена, ₽',
                'type' => Field::NUMBER,
                'from' => 500, 'to' => 70000,
                'choose'=> FiltersParser::getChooseValue($filtersSelectedGroupByType, Field::NUMBER, 'cost')
            ];

            foreach ($filtersWithSorting as $filter) {
                $chooseFilter = FiltersParser::getChooseValue($filtersSelectedGroupByType, $filter->type, $filter->slug);
                if (stripos($filter->slug, 'tsvet-') === 0 && $filter->type === Field::SELECT) {
                    $filters['tsvet'][] = ['slug' => $filter->slug, 'label' => $filter->label, 'choose' => $chooseFilter];
                    continue;
                }
                $filters[$filter->slug] = ['slug' => $filter->slug, 'type' => $filter->type, 'label' => $filter->label, 'choose' => $chooseFilter];
            }

            $command = Search\Command::fromQueryRequest($request, $slug);
            $products = $handler->handle($command);

            return $this->render('frontend/catalog/category.html.twig', [
                'header' => $category->getName(),
                'content' => $category->getSeo()->getContent(),
                'seo' => $category->getSeo(),
                'path' => $category->getPath(),
                'category' => $category,
                'categories' => $categories->childsCategory($category->getId()),
                'parent_category' => $category->getParent(),
                'filters' => $filters,
                'products' => $products,
                'style_visible' => $request->query->get('style', 'cards')
            ]);
        } catch (\Exception $e) {
            return $this->redirectToRoute('error.404');
        }
    }


}