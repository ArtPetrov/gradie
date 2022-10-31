<?php

declare(strict_types=1);

namespace App\Model\DesignProject\UseCase\Edit;

use App\Model\DesignProject\Entity\Project as EntityProject;
use App\Model\DesignProject\UseCase\Client;
use App\Model\DesignProject\UseCase\Project;


class Command
{
    public $id;

    public $client;
    public $project;

    public $sizes;

    public $status;
    public $comment;

    public static function fromProject(EntityProject $project): self
    {
        $command = new self();
        $command->id = $project->getId();
        $command->comment = $project->getComment();
        $command->status = $project->getStatus()->getStatus();
        $command->client = Client\Command::fromProject($project->getClient());
        $command->project = Project\Command::fromProject($project->getInfo());
        return $command;
    }


}
