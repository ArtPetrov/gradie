<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Category;

use App\Model\Ecommerce\Repository\CategoryRepository;
use Symfony\Component\Form\DataTransformerInterface;
use App\Model\Ecommerce\UseCase\Product\Category;

class CategoryTransformer implements DataTransformerInterface
{
    private $categories;

    public function __construct(CategoryRepository $categories)
    {
        $this->categories = $categories;
    }

    public function transform($value)
    {
        return $value;
    }

    /**
     * @return Command
     * @var Category\Command $value
     */
    public function reverseTransform($value)
    {
        if (!in_array($value->main, $value->categories) && $value->main) {
            $value->categories[] = $value->main;
        }

        foreach ($value->categories as $index => $id) {
            $value->categories[$index] = $this->categories->get((int)$id);
        }

        return $value;
    }
}
