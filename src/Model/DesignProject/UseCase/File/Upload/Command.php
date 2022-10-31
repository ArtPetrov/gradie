<?php

declare(strict_types=1);

namespace App\Model\DesignProject\UseCase\File\Upload;

class Command
{
    public $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

}
