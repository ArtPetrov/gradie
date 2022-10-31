<?php

declare(strict_types=1);

namespace App\Model\DesignProject\UseCase\Create\Type4;

use App\Model\DesignProject\UseCase\Client;
use App\Model\DesignProject\UseCase\Project;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\Valid()
     * @var Client\Command
     */
    public $client;

    /**
     * @Assert\Valid()
     * @var Client\Command
     */
    public $project;

    public $files;

    public function __construct()
    {
        $this->client = new Client\Command();
        $this->project = new Project\Command();
        $this->files = new ArrayCollection();
    }
}
