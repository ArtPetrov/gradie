<?php

declare(strict_types=1);

namespace App\Model\Works\UseCase\Edit;

use App\Model\Ecommerce\Repository\ProductRepository;
use App\Model\Works\Entity\ImageDiy;
use App\Model\Works\Repository\WorkRepository;
use App\Model\File\Repository;
use App\Model\Works\Entity\Image;
use App\Model\Works\Entity\Composition;
use App\Model\Works\Entity\Work;
use App\Model\Works\Entity\Seo;
use App\Model\Works\Entity\Content;
use App\Model\Flusher;

class Handler
{
    private $flusher;
    private $works;
    private $files;
    private $tmpFiles;
    private $products;

    public function __construct(Flusher $flusher, WorkRepository $works, ProductRepository $products, Repository\FileRepository $files, Repository\FileTemporaryRepository $tmpFiles)
    {
        $this->flusher = $flusher;
        $this->works = $works;
        $this->files = $files;
        $this->tmpFiles = $tmpFiles;
        $this->products = $products;
    }

    public function handle(Command $command): void
    {
        $work = $this->works->get($command->id);

        $content = new Content(
            $command->content->name,
            $command->content->header,
            $command->content->content,
            $command->content->price
        );
        $work->setContent($content);

        $seo = new Seo(
            $command->seo->title,
            $command->seo->keywords,
            $command->seo->description
        );
        $work->updateSeo($seo);

        $this->images($command, $work);
        $this->composition($command, $work);
        $this->diy($command, $work);

        $this->flusher->flush($work);
    }


    private function composition(Command $command, Work $work)
    {
        $currentElements = array_flip(array_map(static function (Composition $rec): int {
            return $rec->getProduct()->getId();
        }, $work->getComposition()));

        foreach ($command->composition as $element) {
            /** @var Composition $available */
            foreach ($work->getComposition() as $available) {
                if ($available->getProduct()->getId() === (int)$element->id) {
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

            $work->addInComposition(new Composition(
                $work,
                $this->products->get((int)$element->id),
                (int)$element->count,
                (int)$element->position
            ));
        }

        foreach ($work->getComposition() as $available) {
            if (array_key_exists($available->getProduct()->getId(), $currentElements)) {
                $work->removeFromComposition($available);
            }
        }
    }

    private function images(Command $command, Work $work)
    {
        $flagCover = 0;
        $currentImages = array_flip(array_map(static function (Image $rec): int {
            return $rec->getFile()->getId();
        }, $work->getImages()));

        foreach ($command->images as $image) {
            $flagCover += (int)$image->cover;
            /** @var Image $img */
            foreach ($work->getImages() as $img) {
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
            $work->addImage(new Image(
                $work,
                $this->files->get((int)$image->id),
                (bool)$image->cover,
                (int)$image->position
            ));
            $this->tmpFiles->removeByFile($file);
        }
        /** @var Image $available */
        foreach ($work->getImages() as $available) {
            if (array_key_exists($available->getFile()->getId(), $currentImages)) {
                $work->removeImage($available);
            }
        }

        if (0 < count($command->images) && 0 === $flagCover) {
            throw new \DomainException('work.not.set.cover');
        }
    }

    private function diy(Command $command, Work $work)
    {
        $currentImages = array_flip(array_map(static function (ImageDiy $rec): int {
            return $rec->getFile()->getId();
        }, $work->getDiys()));

        foreach ($command->diy as $image) {
            /** @var ImageDiy $img */
            foreach ($work->getDiys() as $img) {
                if ($img->getFile()->getId() === (int)$image->id) {
                    if ($img->getPosition() !== (int)$image->position) {
                        $img->setPosition((int)$image->position);
                    }
                    unset($currentImages[(int)$image->id]);
                    continue 2;
                }
            }
            $file = $this->files->get((int)$image->id);
            $work->addDiy(new ImageDiy(
                $work,
                $this->files->get((int)$image->id),
                (int)$image->position
            ));
            $this->tmpFiles->removeByFile($file);
        }
        /** @var ImageDiy $available */
        foreach ($work->getDiys() as $available) {
            if (array_key_exists($available->getFile()->getId(), $currentImages)) {
                $work->removeDiy($available);
            }
        }
    }
}