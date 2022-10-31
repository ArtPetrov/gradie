<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Information;

use App\Model\Ecommerce\Entity\Product\Product;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public $id;

    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255)
     */
    public $name;

    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255)
     */
    public $article;

    public $content;
    public $youtube;

    public $enable = true;

    /**
     * @Assert\NotNull(message="not.null")
     */
    public $price;
    public $priceFinal = true;
    public $priceOld;

    /**
     * @Assert\NotNull(message="not.null")
     */
    public $weight;
    public $weightIsFinal = true;

    /**
     * @Assert\NotNull(message="not.null")
     */
    public $volume;
    public $volumeIsFinal = true;


    public static function fromProduct(Product $product): self
    {
        $command = new self();

        $command->enable = $product->getEnableStatus();
        $command->name = $product->getInfo()->getName();
        $command->article = $product->getInfo()->getArticle();
        $command->content = $product->getInfo()->getContent();
        $command->youtube = $product->getInfo()->getYoutubeLink();

        $command->volume = $product->getInfo()->getVolume();
        $command->volumeIsFinal = $product->getInfo()->isFinalVolume();

        $command->weight = $product->getInfo()->getWeight();
        $command->weightIsFinal = $product->getInfo()->isFinalWeight();

        $command->price = $product->getPrice()->getCurrent();
        $command->priceOld = $product->getPrice()->getOld();
        $command->priceFinal = $product->getPrice()->isFinalPrice();

        return $command;
    }

}
