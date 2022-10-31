<?php

declare(strict_types=1);

namespace App\Twig\Frontend;

use App\Model\Article\ReadModel\ArticleFetcher;
use App\Model\Ecommerce\ReadModel\Product\ProductFetcher;
use App\Model\Gallery\ReadModel\AlbumFetcher;
use App\Model\Works\ReadModel\WorkFetcher;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class IndexWidget extends AbstractExtension
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('index_news', [$this, 'lastNews'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('index_gallery', [$this, 'lastAlbum'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('index_works', [$this, 'lasWorks'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('index_products', [$this, 'popularProducts'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    public function lastNews(Environment $twig, int $count = 5): string
    {
        $news = $this->container->get(ArticleFetcher::class);
        return $twig->render('widget/frontend/index_news.html.twig', [
            'articles' => $news->getLast($count)
        ]);
    }

    public function lastAlbum(Environment $twig, int $count = 10): string
    {
        $album = $this->container->get(AlbumFetcher::class);
        return $twig->render('widget/frontend/index_albums.html.twig', [
            'galleries' => $album->getLast($count)
        ]);
    }

    public function lasWorks(Environment $twig, int $count = 4): string
    {
        $works = $this->container->get(WorkFetcher::class);
        return $twig->render('widget/frontend/index_works.html.twig', [
            'works' => $works->getLast($count)
        ]);
    }

    public function popularProducts(Environment $twig, int $count = 6): string
    {
        $products = $this->container->get(ProductFetcher::class);
        return $twig->render('widget/frontend/index_products.html.twig', [
            'products' => $products->findRecommendedByPathCategories('/gotovye-resheniia/',$count,'popular')
        ]);
    }
}
