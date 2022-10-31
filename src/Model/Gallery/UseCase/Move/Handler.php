<?php

declare(strict_types=1);

namespace App\Model\Gallery\UseCase\Move;

use App\Model\Flusher;
use App\Model\Gallery\Repository\AlbumRepository;

class Handler
{
    private $flusher;
    private $gallery;

    public function __construct(Flusher $flusher, AlbumRepository $gallery)
    {
        $this->flusher = $flusher;
        $this->gallery = $gallery;
    }

    public function handle(Command $command): void
    {
        $album = $this->gallery->get($command->id);
        $currentPosition = $album->getPosition();
        $friendlyAlbumId = $this->gallery->findByPosition($currentPosition, $command->direction);

        if ($friendlyAlbumId === 0) {
            throw new \DomainException('position.extreme');
        }

        $friendlyAlbum = $this->gallery->get($friendlyAlbumId);

        $album->setPosition($friendlyAlbum->getPosition());
        $friendlyAlbum->setPosition($currentPosition);
        $this->flusher->flush();
    }
}
