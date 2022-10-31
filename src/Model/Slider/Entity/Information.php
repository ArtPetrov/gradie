<?php

declare(strict_types=1);

namespace App\Model\Slider\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Information
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $header;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    public function __construct(string $header, string $description)
    {
        $this->header = $header;
        $this->description = $description;
    }

    public function getHeader(): string
    {
        return $this->header;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
