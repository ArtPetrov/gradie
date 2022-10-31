<?php

declare(strict_types=1);

namespace App\Twig\Frontend;

use App\Model\Ecommerce\ReadModel\Product\ProductFetcher;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ProductRecommendedWidget extends AbstractExtension
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('recommended_for_categories', [$this, 'productsForCategory'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('recommended_for_basket', [$this, 'productsForBasket'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('recommended_for_product', [$this, 'productsForProduct'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    public function productsForBasket(Environment $twig, ?string $token = null): string
    {
        $products = $this->container->get(ProductFetcher::class);

        return $twig->render('widget/frontend/recommended_basket.html.twig', [
            'products' => $products->findRecommendedByBasketToken($token)
        ]);
    }

    public function productsForCategory(Environment $twig, string $path = ''): string
    {
        $products = $this->container->get(ProductFetcher::class);
        return $this->render($twig,$products->findRecommendedByPathCategories($path));
    }

    public function productsForProduct(Environment $twig, int $id): string
    {
        $products = $this->container->get(ProductFetcher::class);
        return $this->render($twig,$products->findRecommendedByIdProducts($id));
    }

    private function render(Environment $twig, array $products): string
    {
        return $twig->render('widget/frontend/recommended.html.twig', [
            'products' => $products
        ]);
    }
}
