<?php

declare(strict_types=1);

namespace App\Model\Gallery\UseCase\Remove;

use App\Model\Gallery\Repository\AlbumRepository;
use App\Model\Flusher;

class Handler
{
    private $gallery;
    private $flusher;

    public function __construct(AlbumRepository $gallery, Flusher $flusher)
    {
        $this->gallery = $gallery;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $album = $this->gallery->get($command->id);
        $this->gallery->remove($album);
        $this->flusher->flush();
    }
}
