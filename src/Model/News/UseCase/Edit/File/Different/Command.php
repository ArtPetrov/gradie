<?php

declare(strict_types=1);

namespace App\Model\News\UseCase\Edit\File\Different;

class Command
{
    public $oldFiles;
    public $currentFiles;

    public function __construct(array $oldFiles, array $currentFiles)
    {
        $this->oldFiles = $oldFiles;
        $this->currentFiles = $currentFiles;
    }
}
