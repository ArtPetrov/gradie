<?php

declare(strict_types=1);

namespace App\Model\Gallery\UseCase\Image\Upload;

class Command
{
    public $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

}
