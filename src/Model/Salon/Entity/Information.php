<?php

declare(strict_types=1);

namespace App\Model\Salon\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Information
{
    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $timetable;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $site;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comment;

    public function __construct(?string $address = '', ?string $name = '', ?string $email = '', ?string $phone = '', ?string $site = '', ?string $timetable = '', ?string $comment = '')
    {
        $this->address = $address;
        $this->name = $name;
        $this->timetable = $timetable;
        $this->phone = $phone;
        $this->email = $email;
        $this->site = $site;
        $this->comment = $comment;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getTimetable(): ?string
    {
        return $this->timetable;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getSite(): ?string
    {
        return $this->site;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }
}
