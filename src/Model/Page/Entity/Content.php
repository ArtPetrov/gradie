<?php

declare(strict_types=1);

namespace App\Model\Page\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Content
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $header;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $body;

    public function __construct(?string $header, ?string $body)
    {
        $this->header = $header;
        $this->body = $body;
    }

    public function getHeader(): ?string
    {
        return $this->header;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }
}
