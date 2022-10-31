<?php

declare(strict_types=1);

namespace App\Model\Buyer\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Information
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $phone;

    public function __construct(?string $email, ?string $password, ?string $name = '', ?string $phone = '')
    {
        $this->email = $email?mb_strtolower($email):null;
        $this->password = $password;
        $this->name = $name;
        $this->phone = $phone;
    }

    public function changePassword(string $hash): self
    {
        $this->password = $hash;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }
}
