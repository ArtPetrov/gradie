<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Edit;

use App\Model\Ecommerce\Entity\Product;
use App\Model\Ecommerce\Entity\Category;
use App\Model\Ecommerce\Helper\ArrayCollectionHelper;
use App\Model\Ecommerce\ReadModel\Product\ProductFetcher;
use App\Model\Ecommerce\Repository\ProductRepository;
use App\Model\Ecommerce\UseCase\Product\Attribute\Validator;
use App\Model\Flusher;
use App\Model\File\Repository;

class Handler
{
    private $flusher;
    private $products;
    private $productFetcher;
    private $files;
    private $tmpFiles;

    public function __construct(Flusher $flusher, ProductRepository $products, ProductFetcher $productFetcher, Repository\FileRepository $files, Repository\FileTemporaryRepository $tmpFiles)
    {
        $this->flusher = $flusher;
        $this->products = $products;
        $this->productFetcher = $productFetcher;
        $this->files = $files;
        $this->tmpFiles = $tmpFiles;
    }

    public function handle(Command $command): void
    {
        if (!$command->information->priceFinal ||
            !$command->information->weightIsFinal ||
            !$command->information->volumeIsFinal
        ) {
            if ($this->productFetcher->existsInComposition($command->id)) {
                throw new \DomainException('product.error.for.composition');
            }
        }

        $product = $this->products->get($command->id);

        $product->updatePopular((int)$command->popular);

        $weight = new Product\Weight($command->information->weight, $command->information->weightIsFinal);
        $volume = new Product\Volume($command->information->volume, $command->information->volumeIsFinal);

        if ($command->information->enable) {
            $product->enable();
        } else {
            if ($this->productFetcher->existsInRecommended($command->id)) {
                throw new \DomainException('product.error.for.recommended');
            }
            $product->disable();
        }

        $information = new Product\Information(
            $command->information->name,
            $command->information->article,
            $weight, $volume,
            $command->information->content,
            $command->information->youtube
        );

        if($command->information->youtube){
            if(!$information->isAvailableYoutube()){
                throw new \DomainException('product.error.youtube.link');
            }
        }

        $product->updateInfo($information);

        $price = new Product\Price(
            $command->information->price,
            $command->information->priceFinal,
            $command->information->priceOld
        );
        $product->changePrice($price);

        $seo = new Product\Seo(
            $command->seo->title,
            $command->seo->keywords,
            $command->seo->description,
        );
        $product->updateSeo($seo);

        $this->recommended($command, $product);
        $this->composition($command, $product);
        $this->images($command, $product);
        $this->categories($command, $product);

        if (!ArrayCollectionHelper::equalValues($product->getAttributes(), $command->attributes)) {
            Validator::validValues($command->attributes);
            $product->updateAttributes($command->attributes);
        }

        $this->flusher->flush($product);
    }

    private function recommended(Command $command, Product\Product $product)
    {
        $currentRecommended = array_flip(array_map(static function (Product\Recommended $rec): int {
            return $rec->getRecommended()->getId();
        }, $product->getRecommended()));

        foreach ($command->recommended as $recommend) {
            /** @var Product\Recommended $available */
            foreach ($product->getRecommended() as $available) {
                if ($available->getRecommended()->getId() === (int)$recommend->id) {
                    if ($available->getPosition() !== (int)$recommend->position) {
                        $available->setPosition((int)$recommend->position);
                    }
                    unset($currentRecommended[(int)$recommend->id]);
                    continue 2;
                }
            }

            $product->addRecommended(new Product\Recommended(
                $product,
                $this->products->get((int)$recommend->id),
                (int)$recommend->position
            ));
        }

        foreach ($product->getRecommended() as $available) {

            if (array_key_exists($available->getRecommended()->getId(), $currentRecommended)) {
                $product->removeRecommended($available);
            }
        }
    }

    private function composition(Command $command, Product\Product $product)
    {
        $currentElements = array_flip(array_map(static function (Product\Composition $rec): int {
            return $rec->getElement()->getId();
        }, $product->getComposition()));

        foreach ($command->composition as $element) {
            if ((int)$element->id === $product->getId()) {
                throw new \DomainException("product.error.composition.myself");
            }
            /** @var Product\Composition $available */
            foreach ($product->getComposition() as $available) {
                if ($available->getElement()->getId() === (int)$element->id) {
                    if ($available->getPosition() !== (int)$element->position) {
                        $available->setPosition((int)$element->position);
                    }
                    if ($available->getCount() !== (int)$element->count) {
                        $available->updateCount((int)$element->count);
                    }
                    unset($currentElements[(int)$element->id]);
                    continue 2;
                }
            }

            $product->addInComposition(new Product\Composition(
                $product,
                $this->products->get((int)$element->id),
                (int)$element->count,
                (int)$element->position
            ));
        }

        foreach ($product->getComposition() as $available) {

            if (array_key_exists($available->getElement()->getId(), $currentElements)) {
                $product->removeFromComposition($available);
            }
        }
    }

    private function images(Command $command, Product\Product $product)
    {
        $flagCover = 0;
        $currentImages = array_flip(array_map(static function (Product\Image $rec): int {
            return $rec->getFile()->getId();
        }, $product->getImages()));

        foreach ($command->images as $image) {
            $flagCover += (int)$image->cover;
            /** @var Product\Image $img */
            foreach ($product->getImages() as $img) {
                if ($img->getFile()->getId() === (int)$image->id) {
                    if ($img->getPosition() !== (int)$image->position) {
                        $img->setPosition((int)$image->position);
                    }
                    if ($img->isCover() !== (bool)$image->cover) {
                        $img->setCover((bool)$image->cover);
                    }
                    unset($currentImages[(int)$image->id]);
                    continue 2;
                }
            }
            $file = $this->files->get((int)$image->id);
            $product->addImage(new Product\Image(
                $product,
                $this->files->get((int)$image->id),
                (bool)$image->cover,
                (int)$image->position
            ));
            $this->tmpFiles->removeByFile($file);
        }
        /** @var Product\Image $available */
        foreach ($product->getImages() as $available) {
            if (array_key_exists($available->getFile()->getId(), $currentImages)) {
                $product->removeImage($available);
            }
        }

        if (0 < count($command->images) && 0 === $flagCover) {
            throw new \DomainException('product.not.set.cover');
        }
    }

    private function categories(Command $command, Product\Product $product)
    {
        $currentCategories = array_flip(array_map(static function (Product\Category $rec): int {
            return $rec->getCategory()->getId();
        }, $product->getCategories()));

        /** @var Category\Category $category */
        foreach ($command->categories->categories as $category) {
            /** @var Product\Category $cat */
            foreach ($product->getCategories() as $cat) {

                if ($cat->getCategory()->getId() !== $category->getId()) {
                    continue;
                }
                if ($cat->isMain() && (int)$command->categories->main !== $cat->getCategory()->getId()) {
                    $cat->disableMain();
                }

                if (!$cat->isMain() && (int)$command->categories->main === $cat->getCategory()->getId()) {
                    $cat->enableMain();
                }
                unset($currentCategories[(int)$category->getId()]);
                continue 2;
            }

            $isMain = (int)$command->categories->main === $category->getId();
            $product->addCategories(new Product\Category($category, $product, $isMain));

        }
        /** @var Product\Category $available */
        foreach ($product->getCategories() as $available) {
            if (array_key_exists($available->getCategory()->getId(), $currentCategories)) {
                $product->removeCategories($available);
            }
        }

        if (0 < count($command->categories->categories) && !$command->categories->main) {
            throw new \DomainException('product.not.set.main.category');
        }
    }
}
