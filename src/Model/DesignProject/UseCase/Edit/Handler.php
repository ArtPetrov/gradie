<?php

declare(strict_types=1);

namespace App\Model\DesignProject\UseCase\Edit;

use App\Model\DesignProject\Entity\Status;
use App\Model\DesignProject\Repository\ProjectRepository;
use App\Model\Flusher;

class Handler
{
    private $flusher;
    private $projects;


    public function __construct(Flusher $flusher, ProjectRepository $projects)
    {
        $this->flusher = $flusher;
        $this->projects = $projects;
    }

    public function handle(Command $command): void
    {

        $status = new Status($command->status);
        $project = $this->projects->get($command->id);
        $project->setStatus($status);
        $project->updateComment($command->comment);
        $this->flusher->flush($project);
    }
}
