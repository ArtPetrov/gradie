<?php

declare(strict_types=1);

namespace App\Model\Ticket\Entity\Message;

use App\Model\Cpanel\Entity\Administrator;
use App\Model\Dealer\Entity\Dealer;
use App\Model\File\Entity\File;
use App\Model\Ticket\Entity\Ticket\Ticket;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Model\Ticket\Repository\MessageRepository")
 * @ORM\Table(name="ticket_message")
 */
class Message
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $content;

    /**
     * @ORM\Embedded(class="Type", columnPrefix="message_")
     */
    private $type;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Files", mappedBy="message", orphanRemoval=true, fetch="EAGER", cascade={"all"})
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id")
     */
    private $files;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Ticket\Entity\Ticket\Ticket", inversedBy="messages", cascade={"persist"})
     * @ORM\JoinColumn(name="ticket_id", nullable=false)
     */
    private $ticket;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Dealer\Entity\Dealer")
     * @ORM\JoinColumn(name="dealer_id", referencedColumnName="id")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Cpanel\Entity\Administrator", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="support_id", referencedColumnName="id")
     */
    private $support;

    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->type = new Type();
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function files(): ?Collection
    {
        return $this->files;
    }

    public function type(): Type
    {
        return $this->type;
    }

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(Ticket $ticket = null): self
    {
        $this->ticket = $ticket;
        return $this;
    }

    public function addFile(Files $file): self
    {
        if ($this->files->contains($file)) {
            return $this;
        }
        $this->files[] = $file;
        $file->setMessage($this);
        return $this;
    }

    public function deleteFile(Files $file): self
    {
        $this->files->removeElement($file);
        $file->setMessage(null);
        return $this;
    }

    public function setAuthor(?Dealer $dealer): self
    {
        $this->author = $dealer;
        return $this;
    }

    public function setSupport(?Administrator $administrator): self
    {
        $this->support = $administrator;
        return $this;
    }

    public function getAuthor(): ?Dealer
    {
        return $this->author;
    }

    public function getSupport(): ?Administrator
    {
        return $this->support;
    }
}
