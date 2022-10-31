<?php

declare(strict_types=1);

namespace App\Model\DesignProject\UseCase\Remove;

use App\Model\DesignProject\Repository\ProjectRepository;
use App\Model\Flusher;

class Handler
{
    private $projects;
    private $flusher;

    public function __construct(ProjectRepository $projects, Flusher $flusher)
    {
        $this->projects = $projects;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $project = $this->projects->get($command->id);
        $this->projects->remove($project);
        $this->flusher->flush();
    }
}
