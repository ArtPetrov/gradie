<?php

declare(strict_types=1);

namespace App\Model\Dealer\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class RequestData
{
    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $company;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $leader;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $profile;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $whyWe;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $howKnow;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $experience;

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getLeader(): ?string
    {
        return $this->leader;
    }

    public function setLeader(?string $leader): self
    {
        $this->leader = $leader;
        return $this;
    }

    public function getProfile(): ?string
    {
        return $this->profile;
    }

    public function setProfile(?string $profile): self
    {
        $this->profile = $profile;
        return $this;
    }

    public function getWhyWe(): ?string
    {
        return $this->whyWe;
    }

    public function setWhyWe(?string $why): self
    {
        $this->whyWe = $why;
        return $this;
    }

    public function getHowKnow(): ?string
    {
        return $this->howKnow;
    }

    public function setHowKnow(?string $how): self
    {
        $this->howKnow = $how;
        return $this;
    }

    public function getExperience(): ?string
    {
        return $this->experience;
    }

    public function setExperience(?string $experience): self
    {
        $this->experience = $experience;
        return $this;
    }
}
