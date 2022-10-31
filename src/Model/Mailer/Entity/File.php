<?php

declare(strict_types=1);

namespace App\Model\Mailer\Entity;

use App\Model\File\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Model\Mailer\Repository\FileRepository")
 * @ORM\Table(name="mailer_files")
 */
class File
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Mail", inversedBy="files", cascade={"all"})
     * @ORM\JoinColumn(name="mail_id")
     */
    private $mail;

    /**
     * @ORM\OneToOne(targetEntity="App\Model\File\Entity\File",cascade = {"all"}, orphanRemoval=true, fetch="EAGER")
     * @ORM\JoinColumn(name="file_id", nullable=false)
     */
    private $file;

    public function __construct(Entity\File $file)
    {
        $this->file = $file;
    }

    public function setMail(?Mail $mail): self
    {
        $this->mail = $mail;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getMail(): ?Mail
    {
        return $this->mail;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getFile(): ?Entity\File
    {
        return $this->file;
    }
}
