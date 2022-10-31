<?php

declare(strict_types=1);

namespace App\Model\Gallery\UseCase\Create;

use App\Model\Gallery\Repository\AlbumRepository;
use App\Model\File\Repository;
use App\Model\Gallery\Entity\Image;
use App\Model\Gallery\Entity\Album;
use App\Model\Gallery\Entity\Seo;
use App\Model\Gallery\Entity\Name;
use App\Model\Flusher;

class Handler
{
    private $flusher;
    private $gallery;
    private $files;
    private $tmpFiles;

    public function __construct(Flusher $flusher, AlbumRepository $gallery, Repository\FileRepository $files, Repository\FileTemporaryRepository $tmpFiles)
    {
        $this->flusher = $flusher;
        $this->gallery = $gallery;
        $this->files = $files;
        $this->tmpFiles = $tmpFiles;
    }

    public function handle(Command $command): void
    {
        $seo = new Seo($command->seo->title, $command->seo->keywords, $command->seo->description);
        $name = new Name($command->name->full, $command->name->short);

        $album = Album::create($name, $seo);
        $this->gallery->add($album);

        $coverFlag = 0;
        foreach ($command->images->getValues() as $image) {
            $file = $this->files->get((int)$image->id);
            $coverFlag += (int)$image->cover;
            $img = new Image(
                $album,
                $file,
                (bool)$image->cover,
                (int)$image->position
            );
            $album->addImage($img);
            $this->tmpFiles->removeByFile($file);
        }

        if ($coverFlag === 0 && $command->images->count() > 0) {
            throw new \DomainException('album.not.set.cover');
        }

        $this->flusher->flush($album);
    }
}