<?php

declare(strict_types=1);

namespace App\Model\Mailer\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Model\Mailer\Repository\RecipientRepository")
 * @ORM\Table(name="mailer_recipient")
 */
class Recipient
{
    public const STATUS_WAIT = 'wait';
    public const STATUS_SENT = 'sent';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Mailer", inversedBy="recipients", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="mailer_id", nullable=false)
     */
    private $mailer;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $status;

    public function __construct(string $email)
    {
        $this->email = $email;
        $this->status = self::STATUS_WAIT;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setMailer(?Mailer $mailer): self
    {
        $this->mailer = $mailer;
        return $this;
    }

    public function getMailer(): ?Mailer
    {
        return $this->mailer;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function sent(): self
    {
        $this->status = self::STATUS_SENT;
        return $this;
    }
}
