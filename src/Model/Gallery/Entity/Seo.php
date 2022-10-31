<?php

declare(strict_types=1);

namespace App\Model\Gallery\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Model\Gallery\Contract;

/**
 * @ORM\Embeddable
 */
class Seo implements Contract\Seo
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $keywords;

    public function __construct(?string $title, ?string $keywords, ?string $description)
    {
        $this->title = $title;
        $this->keywords = $keywords;
        $this->description = $description;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
