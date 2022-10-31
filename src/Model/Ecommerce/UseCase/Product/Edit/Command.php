<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Edit;

use App\Model\Ecommerce\Entity\Product\Product;
use App\Model\Ecommerce\Entity\Product\Recommended;
use App\Model\Ecommerce\Entity\Product\Composition;
use App\Model\Ecommerce\UseCase;
use App\Model\Ecommerce\UseCase\Product\Seo;
use App\Model\Ecommerce\UseCase\Product\Category;
use App\Model\Ecommerce\UseCase\Product\Information;
use App\Model\Ecommerce\UseCase\Product\Image;
use App\Model\Ecommerce\UseCase\Product\Attribute;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotNull()
     */
    public $id;

    /**
     * @Assert\Valid()
     * @var Seo\Command
     */
    public $seo;

    /**
     * @Assert\Valid()
     * @var Information\Command
     */
    public $information;

    public $popular = 0;

    public $images;
    public $attributes;
    public $composition;
    public $recommended;
    public $categories;

    public static function fromProduct(Product $product): self
    {
        $command = new self();
        $command->popular = $product->getPopular();
        $command->id = $product->getId();
        $command->information = Information\Command::fromProduct($product);
        $command->seo = Seo\Command::fromCategorySeo($product->getSeo());

        $command->recommended = new ArrayCollection(array_map(static function (Recommended $product): UseCase\Product\Recommended\Command {
            return UseCase\Product\Recommended\Command::fromRecommended($product);
        }, $product->getRecommended()));

        $command->composition = new ArrayCollection(array_map(static function (Composition $product): UseCase\Product\Composition\Command {
            return UseCase\Product\Composition\Command::fromComposition($product);
        }, $product->getComposition()));

        $command->attributes = new ArrayCollection(array_map(static function ($field) {
            return Attribute\Command::fromAttribute($field);
        }, $product->getAttributes()->toArray()));

        $command->images = new ArrayCollection(array_map(static function ($field) {
            return Image\Command::fromProduct($field);
        }, $product->getImages()));

        $command->categories = Category\Command::fromCategories($product->getCategories());

        return $command;
    }
}
