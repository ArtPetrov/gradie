<?php

declare(strict_types=1);

namespace App\Model\Ticket\Entity\Message;

use App\Model\File\Entity\File;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Model\Ticket\Repository\MessageRepository")
 * @ORM\Table(name="ticket_message_files")
 */
class Files
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="files", cascade={"all"})
     * @ORM\JoinColumn(name="message_id", nullable=false)
     */
    private $message;

    /**
     * @ORM\OneToOne(targetEntity="App\Model\File\Entity\File",cascade = {"all"}, orphanRemoval=true, fetch="EAGER")
     * @ORM\JoinColumn(name="file_id", nullable=false)
     */
    private $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;
        return $this;
    }

    public function setMessage(?Message $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

}
