<?php

declare(strict_types=1);

namespace App\Model\Salon\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Coordinates
{
    /**
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $lat;
    /**
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $lon;

    public function __construct(string $lat, string  $lon)
    {
        $this->lat = $lat;
        $this->lon = $lon;
    }

    public function getLon(): string
    {
        return $this->lon;
    }

    public function getLat(): string
    {
        return $this->lat;
    }
}