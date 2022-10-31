<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Image;

use App\Model\Ecommerce\Entity\Product\Image;

class Command
{
    public $id;
    public $cover;
    public $src;
    public $position;

    public static function fromProduct(Image $img): self
    {
        $command = new self();
        $command->id = $img->getFile()->getId();
        $command->src = $img->getFile()->getPath();
        $command->cover = $img->isCover();
        $command->position = $img->getPosition();
        return $command;
    }
}
