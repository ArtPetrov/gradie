<?php

declare(strict_types=1);

namespace App\Twig\Frontend;

use App\Model\PopularProducts\ReadModel\PopularFetcher;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PopularWidget extends AbstractExtension
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('popular_products', [$this, 'popularProducts'], ['needs_environment' => true, 'is_safe' => ['html']])
        ];
    }

    public function popularProducts(Environment $twig, int $count = 6): string
    {
        $products = $this->container->get(PopularFetcher::class);

        return $twig->render('widget/frontend/index_popular.html.twig', [
            'products' => $products->getAllWithLimit($count)
        ]);
    }

}
