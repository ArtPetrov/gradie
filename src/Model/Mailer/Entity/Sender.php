<?php

declare(strict_types=1);

namespace App\Model\Mailer\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Sender
{
    /**
     * @ORM\Column(type="string", length=128)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=128, nullable=false)
     */
    private $email;

    public function __construct(string $email, string $name = null)
    {
        $this->email = $email;
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function changeName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function changeEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
}
