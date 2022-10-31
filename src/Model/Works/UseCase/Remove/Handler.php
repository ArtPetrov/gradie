<?php

declare(strict_types=1);

namespace App\Model\Works\UseCase\Remove;

use App\Model\Flusher;
use App\Model\Works\Repository\WorkRepository;

class Handler
{
    private $works;
    private $flusher;

    public function __construct(WorkRepository $works, Flusher $flusher)
    {
        $this->works = $works;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $work = $this->works->get($command->id);
        $this->works->remove($work);
        $this->flusher->flush();
    }
}
