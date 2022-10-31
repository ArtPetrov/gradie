<?php

declare(strict_types=1);

namespace App\Model\Lead\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Client
{
    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $city;

    public function __construct(?string $name, ?string $phone, ?string $email, ?string $city)
    {
        $this->name = $name;
        $this->phone = $phone;
        $this->email = $email;
        $this->city = $city;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }
}
