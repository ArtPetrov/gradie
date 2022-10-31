<?php

declare(strict_types=1);

namespace App\Model\Works\UseCase\Image\Diy;

use App\Model\Works\Entity\ImageDiy;

class Command
{
    public $id;
    public $src;
    public $position;

    public static function fromWork(ImageDiy $img): self
    {
        $command = new self();
        $command->id = $img->getFile()->getId();
        $command->src = $img->getFile()->getPath();
        $command->position = $img->getPosition();
        return $command;
    }
}
