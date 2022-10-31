<?php

declare(strict_types=1);

namespace App\Model\Page\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Settings
{
    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $template;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default":200})
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;

    public function __construct(string $slug, ?string $template, ?int $status = 200)
    {
        $this->template = $template;
        $this->status = $status;
        $this->slug = $slug;
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }
}
