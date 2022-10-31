<?php

declare(strict_types=1);

namespace App\Model\File\UseCase\Category\Add;

use App\Model\File\Entity\File;

class Command
{
    public $file;

    public $category;

    public function __construct(File $file, int $category)
    {
        $this->file = $file;
        $this->category = $category;
    }
}
