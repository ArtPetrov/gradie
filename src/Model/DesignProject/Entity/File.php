<?php

declare(strict_types=1);

namespace App\Model\DesignProject\Entity;

use App\Model\File\Entity\File as EntityFile;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="design_project_images",
 * indexes={
 *     @ORM\Index(name="project_indx_dp", columns={"project_id"})
 * })
 */
class File
{
    public const DIRECTORY_FILES = 'design_project';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="files")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id",nullable=false)
     */
    private $project;

    /**
     * @ORM\OneToOne(targetEntity="App\Model\File\Entity\File", cascade = {"persist","remove"}, orphanRemoval=true, fetch="EAGER")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", nullable=false)
     */
    private $file;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    public function __construct(Project $project, EntityFile $file)
    {
        $this->project = $project;
        $this->file = $file;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getFile(): EntityFile
    {
        return $this->file;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }
}
