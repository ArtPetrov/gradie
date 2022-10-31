<?php

declare(strict_types=1);

namespace App\Model\Slider\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Button
{
    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default":false})
     */
    private $enable;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $link;

    public function __construct(bool $enable = false, ?string $label = '', ?string $link = '')
    {
        $this->enable = $enable;
        $this->label = $label;
        $this->link = $link;
    }

    public function isEnable(): bool
    {
        return $this->enable;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }
}
