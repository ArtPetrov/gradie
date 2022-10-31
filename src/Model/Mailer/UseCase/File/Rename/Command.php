<?php

declare(strict_types=1);

namespace App\Model\Mailer\UseCase\File\Rename;

use App\Model\File\Entity\File;

class Command
{
    public $file;
    public $name;

    public function __construct(File $file, string $name)
    {
        $this->file = $file;
        $this->name = $name;
    }
}
