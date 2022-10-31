<?php

declare(strict_types=1);

namespace App\Model\Gallery\UseCase\Image;

use App\Model\Gallery\Entity\Image;

class Command
{
    public $id;
    public $cover;
    public $src;
    public $position;

    public static function fromGallery(Image $img): self
    {
        $command = new self();
        $command->id = $img->getFile()->getId();
        $command->src = $img->getFile()->getPath();
        $command->cover = $img->isCover();
        $command->position = $img->getPosition();
        return $command;
    }
}
