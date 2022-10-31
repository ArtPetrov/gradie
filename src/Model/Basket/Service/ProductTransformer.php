<?php

declare(strict_types=1);

namespace App\Model\Basket\Service;

use App\Model\Ecommerce\Entity\Product\Product;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProductTransformer
{
    /**
     * @var FilterService
     */
    private $filter;
    private $urlGenerator;

    public function __construct(FilterService $filter, UrlGeneratorInterface $urlGenerator)
    {
        $this->filter = $filter;
        $this->urlGenerator = $urlGenerator;
    }

    public function transform(Product $product, int $count = 1, string $filter = 'product_95_95'): array
    {
        return [
            'id' => $product->getId(),
            'link' => $this->urlGenerator->generate('product',['id'=>$product->getId()]),
            'count' => $count,
            'price' => $product->getFinishPrice(),
            'old_price' => $product->getPrice()->getOld(),
            'name' => $product->getInfo()->getName(),
            'cover' => $this->filter->getUrlOfFilteredImage($product->getCover()->getPath(), $filter),
            'article' => $product->getInfo()->getArticle()
        ];
    }
}
