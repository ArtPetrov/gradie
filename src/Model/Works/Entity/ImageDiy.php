<?php

declare(strict_types=1);

namespace App\Model\Works\Entity;

use App\Model\File\Entity\File;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_works_images_diy",
 * indexes={
 *     @ORM\Index(name="position_idx_photo_work_diy", columns={"position"}),
 *     @ORM\Index(name="work_idx_photo_diy", columns={"work_id"}),
 * })
 */
class ImageDiy
{
    public const DIRECTORY_FILES = 'works_photo_diy';
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Work", inversedBy="diy")
     * @ORM\JoinColumn(name="work_id", referencedColumnName="id",nullable=false)
     */
    private $work;

    /**
     * @ORM\OneToOne(targetEntity="App\Model\File\Entity\File", cascade = {"persist","remove"}, orphanRemoval=true, fetch="EAGER")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", nullable=false)
     */
    private $file;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default":0,"unsigned":true})
     */
    private $position;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    public function __construct(Work $work, File $file, int $position = 0)
    {
        $this->work = $work;
        $this->file = $file;
        $this->position = $position;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getWork(): Work
    {
        return $this->work;
    }

    public function getFile(): File
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

    public function getPosition(): int
    {
        return $this->position;
    }
}