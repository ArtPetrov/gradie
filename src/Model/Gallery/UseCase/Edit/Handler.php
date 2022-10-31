<?php

declare(strict_types=1);

namespace App\Model\Gallery\UseCase\Edit;

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
        $album = $this->gallery->get($command->id);

        $name = new Name(
            $command->name->full,
            $command->name->short
        );
        $album->changeName($name);

        $seo = new Seo(
            $command->seo->title,
            $command->seo->keywords,
            $command->seo->description,
        );
        $album->updateSeo($seo);

        $this->images($command, $album);

        $this->flusher->flush($album);
    }


    private function images(Command $command, Album $album)
    {
        $flagCover = 0;
        $currentImages = array_flip(array_map(static function (Image $rec): int {
            return $rec->getFile()->getId();
        }, $album->getImages()));

        foreach ($command->images as $image) {
            $flagCover += (int)$image->cover;
            /** @var Image $img */
            foreach ($album->getImages() as $img) {
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
            $album->addImage(new Image(
                $album,
                $this->files->get((int)$image->id),
                (bool)$image->cover,
                (int)$image->position
            ));
            $this->tmpFiles->removeByFile($file);
        }
        /** @var Image $available */
        foreach ($album->getImages() as $available) {
            if (array_key_exists($available->getFile()->getId(), $currentImages)) {
                $album->removeImage($available);
            }
        }

        if (0 < count($command->images) && 0 === $flagCover) {
            throw new \DomainException('album.not.set.cover');
        }
    }
}