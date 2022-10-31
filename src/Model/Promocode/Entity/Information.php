<?php

declare(strict_types=1);

namespace App\Model\Promocode\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Information
{
    /**
     * @ORM\Column(type="string", length=128, unique=true)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    public function __construct(string $code, string $name, ?string $description)
    {
        $this->code = mb_strtoupper($code);
        $this->name = $name;
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}

