<?php

declare(strict_types=1);

namespace App\Twig\Frontend;

use App\Model\Ecommerce\ReadModel\Product\ProductFetcher;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ProductCardsWidget extends AbstractExtension
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('product_cards', [$this, 'cards'], ['needs_environment' => true, 'is_safe' => ['html']])
        ];
    }

    public function cards(Environment $twig, string $pathCategory = '', string $type='BUY', int $count = 6): string
    {
        $products = $this->container->get(ProductFetcher::class);

        return $twig->render('widget/frontend/product_cards.html.twig', [
            'products' => $products->findRecommendedByPathCategories($pathCategory,$count,'popular'),
            'type' =>$type
        ]);
    }

}
