<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Search;

use App\Model\Ecommerce\Entity\Attribute\Field;
use App\Model\Ecommerce\ReadModel\Attribute\AttributeFetcher;
use App\Model\Ecommerce\ReadModel\Attribute\AttributeForFilter;
use App\Model\Ecommerce\ReadModel\Category\CategoryFetcher;
use App\Model\Ecommerce\ReadModel\Product\ProductFetcher;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Handler
{
    private $products;
    private $categories;
    private $filter;
    private $urlGenerator;
    private $attributes;

    public function __construct(ProductFetcher $products, CategoryFetcher $categories, FilterService $filter, UrlGeneratorInterface $urlGenerator, AttributeFetcher $attributes)
    {
        $this->products = $products;
        $this->categories = $categories;
        $this->filter = $filter;
        $this->urlGenerator = $urlGenerator;
        $this->attributes = $attributes;
    }

    public function handle(Command $command): array
    {
        $category = $this->categories->getByPath($command->category);
        $orderField = $command->sort['slug'] === 'cost' ? 'cost' : 'popular';
        $orderDirection = $command->sort['order'] === 'ASC' ? 'ASC' : 'DESC';

        $products = $this->products->findForSearch(
            $category->getPath(),
            $command->limit,
            $command->offset,
            $orderField,
            $orderDirection,
            $this->filters($command->filters)
        );

        foreach ($products as $index => $product) {
            if ($product->cover) {
                $products[$index]->cover = $this->filter->getUrlOfFilteredImage($product->cover, 'product_500_500');
            }
            $products[$index]->cost = number_format((float)$product->cost, 0, '.', ' ');
            $products[$index]->old_cost = number_format((float)$product->old_cost, 0, '.', ' ');
            $products[$index]->link = $this->urlGenerator->generate('product', ['id' => (int)$product->id]);
        }

        return $products;
    }

    private function filters(array $filters): array
    {
        if (0 === count($filters)) {
            return $filters;
        }

        $attributes = array_map(static function ($f) {
            return $f['slug'];
        }, $filters);

        $availableAttrs = $this->attributes->getByList($attributes);
        if (in_array('cost', $attributes)) {
            $availableAttrs[] = AttributeForFilter::create('cost', Field::NUMBER);
        }

        foreach ($availableAttrs as $index => $attr) {
            $current = (array_values(array_filter($filters, static function ($a) use ($attr) {
                return $attr->slug === $a['slug'];
            })))[0];

            if ($attr->field_type === Field::SELECT || $attr->field_type === Field::CHECKBOX) {
                if (!in_array($current['value'], $attr->values)) {
                    unset($availableAttrs[$index]);
                    continue;
                }
                $availableAttrs[$index]->value = $current['value'];
            }

            if ($attr->field_type === Field::NUMBER) {
                if ((int)$current['to'] > 0) {
                    $availableAttrs[$index]->to = (int)$current['to'];
                }
                if ((int)$current['from'] > 0) {
                    $availableAttrs[$index]->from = (int)$current['from'];
                }
            }
        }
        return $availableAttrs;
    }
}
