<?php

declare(strict_types=1);

namespace App\Model\Order\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Address
{
    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="json", nullable=true, options={"jsonb":true})
     */
    private $fields;

    public function __construct(string $type, string $city, ?array $fields)
    {
        $this->type = $type;
        $this->city = $city;
        $this->fields = json_encode($fields);
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getFields(): ?array
    {
        return json_decode($this->fields, true);
    }
}
