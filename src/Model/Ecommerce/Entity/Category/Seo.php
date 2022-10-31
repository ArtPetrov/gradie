<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Entity\Category;

use App\Model\Ecommerce\Contract;
use Doctrine\ORM\Mapping as ORM;

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

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    public function __construct(string $title, string $keywords, string $description, ?string $content = '')
    {
        $this->title = $title;
        $this->keywords = $keywords;
        $this->description = $description;
        $this->content = $content;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getKeywords(): string
    {
        return $this->keywords;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }
}
