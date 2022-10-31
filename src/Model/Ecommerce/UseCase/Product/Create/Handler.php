<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Create;

use App\Model\Ecommerce\Entity\Category\Category;
use App\Model\Ecommerce\UseCase\Product\Attribute;
use App\Model\Ecommerce\Entity\Product;
use App\Model\Ecommerce\Repository\ProductRepository;
use App\Model\Flusher;
use App\Model\File\Repository;

class Handler
{
    private $flusher;
    private $products;
    private $files;
    private $tmpFiles;

    public function __construct(Flusher $flusher, ProductRepository $products, Repository\FileRepository $files, Repository\FileTemporaryRepository $tmpFiles)
    {
        $this->flusher = $flusher;
        $this->products = $products;
        $this->files = $files;
        $this->tmpFiles = $tmpFiles;
    }

    public function handle(Command $command): void
    {
        $weight = new Product\Weight($command->information->weight, $command->information->weightIsFinal);
        $volume = new Product\Volume($command->information->volume, $command->information->volumeIsFinal);

        $information = new Product\Information(
            $command->information->name,
            $command->information->article,
            $weight, $volume,
            $command->information->content
        );

        $price = new Product\Price(
            $command->information->price,
            $command->information->priceFinal,
            $command->information->priceOld
        );

        $seo = new Product\Seo(
            $command->seo->title,
            $command->seo->keywords,
            $command->seo->description,
        );

        $product = new Product\Product(
            $command->information->enable,
            $information,
            $price,
            $seo,
            $command->attributes,
            (int)$command->popular,
            $command->information->youtube
        );

        if($command->information->youtube){
            if(!$product->getInfo()->isAvailableYoutube()){
                throw new \DomainException('product.error.youtube.link');
            }
        }

        $this->products->add($product);

        foreach ($command->recommended->getValues() as $recommend) {
            $productRecommend = new Product\Recommended(
                $product,
                $this->products->get((int)$recommend->id),
                (int)$recommend->position
            );
            $product->addRecommended($productRecommend);
        }

        foreach ($command->composition->getValues() as $element) {
            $productElement = new Product\Composition(
                $product,
                $this->products->get((int)$element->id),
                (int)$element->count,
                (int)$element->position
            );
            $product->addInComposition($productElement);
        }

        Attribute\Validator::validValues($command->attributes);
        $product->updateAttributes($command->attributes);

        $coverFlag = 0;
        foreach ($command->images->getValues() as $image) {
            $file = $this->files->get((int)$image->id);
            $coverFlag += (int)$image->cover;
            $productImage = new Product\Image(
                $product,
                $file,
                (bool)$image->cover,
                (int)$image->position
            );
            $product->addImage($productImage);
            $this->tmpFiles->removeByFile($file);
        }

        if ($coverFlag === 0 && $command->images->count() > 0) {
            throw new \DomainException('product.not.set.cover');
        }

        if (!$command->categories->main && 0 !== count($command->categories->categories)) {
            throw new \DomainException('product.not.set.main.category');
        }
        /** @var Category $category */
        foreach ($command->categories->categories as $category) {
            $isMain = $category->getId() === (int)$command->categories->main;
            $cat = new Product\Category($category, $product, $isMain);
            $product->addCategories($cat);
        }

        $this->flusher->flush($product);
    }

}
