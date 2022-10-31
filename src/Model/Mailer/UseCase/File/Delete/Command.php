<?php

declare(strict_types=1);

namespace App\Model\Mailer\UseCase\File\Delete;

use App\Model\File\Entity\File;

class Command
{
    public $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }
}
