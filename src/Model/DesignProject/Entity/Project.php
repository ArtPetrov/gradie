<?php

declare(strict_types=1);

namespace App\Model\DesignProject\Entity;

use App\Model\EventBus;
use App\Model\EventBusTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="design_project",
 * indexes={
 *     @ORM\Index(name="date_project_idx", columns={"created_at"}),
 * })
 */
class Project implements EventBus
{
    use EventBusTrait;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Embedded(class="Type", columnPrefix=false)
     */
    private $type;

    /**
     * @ORM\Embedded(class="Client", columnPrefix="client_")
     */
    private $client;

    /**
     * @ORM\Embedded(class="Information", columnPrefix="info_")
     */
    private $information;

    /**
     * @ORM\OneToMany(targetEntity="File", mappedBy="project", orphanRemoval=true, cascade={"persist","remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="project_id")
     */
    private $files;

    /**
     * @ORM\Column(type="design.project.size", nullable=true)
     */
    private $sizes;

    /**
     * @ORM\Embedded(class="Status", columnPrefix=false)
     */
    private $status;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    private function __construct()
    {
        $this->files = new ArrayCollection();
        $this->status = new Status();
        $this->sizes = new ArrayCollection();
    }

    public static function create(Information $info, Client $client, Type $type): self
    {
        $project = new self();
        $project->updateInfo($info);
        $project->updateClient($client);
        $project->setType($type);
        return $project;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getInfo(): Information
    {
        return $this->information;
    }

    public function updateInfo(Information $info): self
    {
        $this->information = $info;
        return $this;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function updateClient(Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function addFile(File $file): self
    {
        if ($this->files->contains($file)) {
            return $this;
        }
        $this->files->add($file);
        return $this;
    }

    public function setType(Type $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getFiles(): array
    {
        return $this->files->toArray();
    }

    public function removeFile(File $file): self
    {
        $this->files->removeElement($file);
        return $this;
    }

    public function setStatus(Status $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setSize(ArrayCollection $sizes): self
    {
        $this->sizes = $sizes;
        return $this;
    }

    public function getSize(): ArrayCollection
    {
        return $this->sizes;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function updateComment(?string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }
}

