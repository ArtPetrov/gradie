<?php

declare(strict_types=1);

namespace App\Model\DesignProject\UseCase\Create\Type4;

use App\Model\DesignProject\Repository\ProjectRepository;
use App\Model\File\Repository;
use App\Model\DesignProject\Entity\File;
use App\Model\DesignProject\Entity\Information;
use App\Model\DesignProject\Entity\Project;
use App\Model\DesignProject\Entity\Client;
use App\Model\DesignProject\Entity\Type;
use App\Model\Flusher;

class Handler
{
    private $flusher;
    private $projects;
    private $files;
    private $tmpFiles;

    public function __construct(Flusher $flusher, ProjectRepository $projects, Repository\FileRepository $files, Repository\FileTemporaryRepository $tmpFiles)
    {
        $this->flusher = $flusher;
        $this->projects = $projects;
        $this->files = $files;
        $this->tmpFiles = $tmpFiles;
    }

    public function handle(Command $command): void
    {
        $info = new Information($command->project->name, $command->project->description);
        $client = new Client($command->client->name, $command->client->phone, $command->client->email, $command->client->city);
        $type = new Type(Type::TYPE_4);

        $project = Project::create($info, $client, $type);
        $this->projects->add($project);

        foreach ($command->files->getValues() as $current) {
            $file = $this->files->get((int)$current->id);
            $project->addFile(new File($project, $file));
            $this->tmpFiles->removeByFile($file);
        }

        $this->flusher->flush($project);
    }
}
