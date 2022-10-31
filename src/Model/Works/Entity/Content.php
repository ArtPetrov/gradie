<?php

declare(strict_types=1);

namespace App\Model\Works\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Content
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $header;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $price;

    public function __construct(string $name, string $header, ?string $content, ?string $price)
    {
        $this->name = $name;
        $this->header = $header;
        $this->content = $content;
        $this->price = $price;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHeader(): string
    {
        return $this->header;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }
}
