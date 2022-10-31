<?php

declare(strict_types=1);

namespace App\Model\Article\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Name
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $short;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $full;

    public function __construct(string $full, string $short)
    {
        $this->full = $full;
        $this->short = $short;
    }

    public function getFull(): string
    {
        return $this->full;
    }

    public function getShort(): string
    {
        return $this->short;
    }
}
