<?php

declare(strict_types=1);

namespace App\Model\Ticket\Entity\Ticket;

use App\Model\Dealer\Entity\Dealer;
use App\Model\Ticket\Entity\Message\Message;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Model\Ticket\Repository\TicketRepository")
 * @ORM\Table(indexes={
 *     @ORM\Index(columns={"process_status"}),
 * })
 */
class Ticket
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $header;

    /**
     * @var ArrayCollection[]
     * @ORM\OneToMany(targetEntity="App\Model\Ticket\Entity\Message\Message", mappedBy="ticket", orphanRemoval=true, cascade={"persist"})
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $messages;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Dealer\Entity\Dealer",  inversedBy="tickets")
     * @ORM\JoinColumn(name="dealer_id", referencedColumnName="id", nullable=false)
     */
    private $author;

    /**
     * @ORM\Embedded(class="Status", columnPrefix="process_")
     */
    private $status;

    /**
     * @ORM\Embedded(class="State", columnPrefix="process_")
     */
    private $state;

    public function __construct(Dealer $author, string $header)
    {
        $this->author = $author;
        $this->header = $header;
        $this->messages = new ArrayCollection();
        $this->status = new Status();
        $this->state = new State();
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

    public function getHeader(): ?string
    {
        return $this->header;
    }

    public function sendMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            return $this;
        }
        $this->messages[] = $message;
        $message->setTicket($this);
        return $this;
    }

    public function deleteMessage(Message $message): self
    {
        $this->messages->removeElement($message);
        $message->setTicket(null);
        return $this;
    }

    public function getMessages(): ?Collection
    {
        return $this->messages;
    }

    public function getAuthor(): ?Dealer
    {
        return $this->author;
    }

    public function status(): Status
    {
        return $this->status;
    }

    public function state(): State
    {
        return $this->state;
    }

}
