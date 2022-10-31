<?php

declare(strict_types=1);

namespace App\Twig\Frontend;

use App\Model\Ecommerce\ReadModel\Category\CategoryFetcher;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CategoryWidget extends AbstractExtension
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('categories_header', [$this, 'categoriesHeader'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('sidebar_category', [$this, 'categoriesSidebar'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('sidebar_for_category', [$this, 'sidebarForCategory'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    private function getCategories(): array
    {
        $fetcher = $this->container->get(CategoryFetcher::class);
        $categories = $fetcher->childsCategory();
        foreach ($categories as $index => $category) {
            $childs = $fetcher->childsCategory($category->id);
            foreach ($childs as $child) {
                $categories[$index]->childs[] = $child;
            }
        }
        return $categories;
    }

    public function categoriesHeader(Environment $twig): string
    {
        return $twig->render('widget/frontend/category.html.twig', [
            'categories' => $this->getCategories()
        ]);
    }

    public function categoriesSidebar(Environment $twig, ?string $path = ''): string
    {
        return $twig->render('widget/frontend/category_sidebar.html.twig', [
            'categories' => $this->getCategories(),
            'current' => $path
        ]);
    }

    public function sidebarForCategory(Environment $twig, string $path): string
    {
        $fetcher = $this->container->get(CategoryFetcher::class);
        $category = $fetcher->getByPath($path);
        $categories = $fetcher->childsCategory($category->getId());

        if (0 === count($categories)) {
            $categories = $category->getParent() ? $fetcher->childsCategory($category->getParent()->getId()) : $fetcher->childsCategory() ;
        }

        return $twig->render('widget/frontend/sidebar_for_category.html.twig', [
            'category' => $category,
            'categories' => $categories,

        ]);
    }
}
