<?php

declare(strict_types=1);

namespace App\Model\Works\UseCase\Image\Upload;

use App\Model\Works\Entity\Image;
use App\Model\Works\Entity\ImageDiy;

class Command
{
    public $file;
    public $directory;

    public function __construct($file, string $type)
    {
        $this->file = $file;
        $this->directory = $this->getDirectory($type);
    }

    private function getDirectory(string $type): string
    {
        return 'images' === $type ? Image::DIRECTORY_FILES : ImageDiy::DIRECTORY_FILES;
    }

}