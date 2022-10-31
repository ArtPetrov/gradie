<?php

declare(strict_types=1);

namespace App\Model\Mailer\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Model\Mailer\Repository\MailerRepository")
 */
class Mailer
{
    use TimestampableEntity;

    public const TYPE_MAIL = 'mail';
    public const TYPE_MAILING = 'mailing';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $type;

    /**
     * @var Process|null
     * @ORM\Embedded(class="Process", columnPrefix="procces_")
     */
    private $process;

    /**
     * @var Mail|null
     * @ORM\OneToOne(targetEntity="Mail", orphanRemoval=true, cascade={"all"})
     * @ORM\JoinColumn(name="mail_id")
     */
    private $mail;

    /**
     * @var Sender|null
     * @ORM\Embedded(class="Sender", columnPrefix="sender_")
     */
    private $sender;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Recipient", mappedBy="mailer", orphanRemoval=true, fetch="EXTRA_LAZY", cascade={"all"})
     * @ORM\JoinColumn(name="id", referencedColumnName="mailer_id")
     */
    private $recipients;

    public function __construct(string $name, Sender $sender, Mail $mail)
    {
        $this->name = $name;
        $this->mail = $mail;
        $this->sender = $sender;
        $this->process = new Process(Process::STATUS_WORK);
        $this->recipients = new ArrayCollection();
        $this->type = self::TYPE_MAILING;
    }

    public function setTypeMail(): self
    {
        $this->type = self::TYPE_MAIL;
        return $this;
    }

    public function setTypeMailing(): self
    {
        $this->type = self::TYPE_MAILING;
        return $this;
    }

    public function isMailType(): bool
    {
        return $this->type === self::TYPE_MAIL;
    }

    public function isMailingType(): bool
    {
        return $this->type === self::TYPE_MAILING;
    }

    public function mail(): Mail
    {
        return $this->mail;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function changeName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function sender(): ?Sender
    {
        return $this->sender;
    }

    public function process(): ?Process
    {
        return $this->process;
    }

    public function addRecipient(Recipient $recipient): self
    {
        if ($this->recipients->contains($recipient)) {
            return $this;
        }
        $this->recipients[] = $recipient;
        $recipient->setMailer($this);
        return $this;
    }

    public function removeRecipient(Recipient $recipient): self
    {
        $this->recipients->removeElement($recipient);
        $recipient->setMailer(null);
        return $this;
    }

    public function recipients(): ?Collection
    {
        return $this->recipients;
    }
}
