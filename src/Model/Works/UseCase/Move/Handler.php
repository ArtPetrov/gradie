<?php

declare(strict_types=1);

namespace App\Model\Works\UseCase\Move;

use App\Model\Flusher;
use App\Model\Works\Repository\WorkRepository;

class Handler
{
    private $flusher;
    private $works;

    public function __construct(Flusher $flusher, WorkRepository $works)
    {
        $this->flusher = $flusher;
        $this->works = $works;
    }

    public function handle(Command $command): void
    {
        $work = $this->works->get($command->id);
        $currentPosition = $work->getPosition();
        $friendlyWorkId = $this->works->findByPosition($currentPosition, $command->direction);

        if ($friendlyWorkId === 0) {
            throw new \DomainException('position.extreme');
        }

        $friendlyWork = $this->works->get($friendlyWorkId);

        $work->setPosition($friendlyWork->getPosition());
        $friendlyWork->setPosition($currentPosition);
        $this->flusher->flush();
    }
}
