<?php

declare(strict_types=1);

namespace App\Model\Works\UseCase\Create;

use App\Model\Ecommerce\Repository\ProductRepository;
use App\Model\Works\Entity\ImageDiy;
use App\Model\Works\Repository\WorkRepository;
use App\Model\File\Repository;
use App\Model\Works\Entity\Image;
use App\Model\Works\Entity\Work;
use App\Model\Works\Entity\Composition;
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

    public function __construct(Flusher $flusher, WorkRepository $works, Repository\FileRepository $files, Repository\FileTemporaryRepository $tmpFiles, ProductRepository $products)
    {
        $this->flusher = $flusher;
        $this->works = $works;
        $this->files = $files;
        $this->tmpFiles = $tmpFiles;
        $this->products = $products;
    }

    public function handle(Command $command): void
    {
        $seo = new Seo($command->seo->title, $command->seo->keywords, $command->seo->description);
        $content = new Content($command->content->name, $command->content->header, $command->content->content, $command->content->price);

        $work = Work::create($content, $seo);
        $this->works->add($work);

        $coverFlag = 0;
        foreach ($command->images->getValues() as $image) {
            $file = $this->files->get((int)$image->id);
            $coverFlag += (int)$image->cover;
            $img = new Image(
                $work,
                $file,
                (bool)$image->cover,
                (int)$image->position
            );
            $work->addImage($img);
            $this->tmpFiles->removeByFile($file);
        }

        if ($coverFlag === 0 && $command->images->count() > 0) {
            throw new \DomainException('work.not.set.cover');
        }

        foreach ($command->diy->getValues() as $image) {
            $file = $this->files->get((int)$image->id);
            $img = new ImageDiy($work, $file, (int)$image->position);
            $work->addDiy($img);
            $this->tmpFiles->removeByFile($file);
        }

        foreach ($command->composition->getValues() as $product) {
            $work->addInComposition(
                new Composition(
                    $work,
                    $this->products->get((int)$product->id),
                    (int)$product->count,
                    (int)$product->position
                ));
        }

        $this->flusher->flush($work);
    }
}
