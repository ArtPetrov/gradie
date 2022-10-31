<?php

declare(strict_types=1);

namespace App\Model\Dealer\Entity;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class InformationDealer
{
    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $name;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $site;
    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $phone;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;
    /**
     * @ORM\Column(type="string", length=12, nullable=true)
     */
    private $inn;

    /**
     * @ORM\Column(type="string", length=12, nullable=true)
     */
    private $kpp;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contrahens;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function changeName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSite(): ?string
    {
        return $this->site;
    }

    public function changeSite(?string $site): self
    {
        $this->site = $site;
        return $this;
    }

    public function changeAddresss(?string $adress): self
    {
        $this->address = $adress;
        return $this;
    }

    public function changePhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getInn(): ?string
    {
        return $this->inn;
    }

    public function changeInn(?string $inn): self
    {
        $this->inn = $inn;
        return $this;
    }

    public function getKpp(): ?string
    {
        return $this->kpp;
    }

    public function changeKpp(?string $kpp): self
    {
        $this->kpp = $kpp;
        return $this;
    }

    public function getContrahens(): ?string
    {
        return $this->contrahens;
    }

    public function changeContrahens(?string $contrahens): self
    {
        $this->contrahens = $contrahens;
        return $this;
    }

}