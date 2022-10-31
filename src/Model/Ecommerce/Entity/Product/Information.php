<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Entity\Product;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Information
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", unique=true, length=128)
     */
    private $article;

    /**
     * @ORM\Column(type="decimal", scale=3, nullable=false, options={"default":0.00, "unsigned"=true})
     */
    private $weight;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default":true})
     */
    private $weightIsFinal;

    /**
     * @ORM\Column(type="decimal", scale=3, nullable=false, options={"default":0.00, "unsigned"=true})
     */
    private $volume;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default":true})
     */
    private $volumeIsFinal;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $youtube;

    public function __construct(string $name, string $article, Weight $weight, Volume $volume, ?string $content = '', ?string $youtube = '')
    {
        $this->name = $name;
        $this->article = $article;
        $this->weight = $weight->getValue();
        $this->weightIsFinal = $weight->isFinal();
        $this->volume = $volume->getValue();
        $this->volumeIsFinal = $volume->isFinal();
        $this->content = $content;
        $this->youtube = $youtube;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getArticle(): string
    {
        return $this->article;
    }

    public function isAvailableYoutube(): bool
    {
        return !is_null($this->getYoutubeHash());
    }

    public function getYoutubeHash(): ?string
    {
        $result = preg_match('/((watch\?v=|\.be\/)(?<HASH>[A-z0-9_-]*))/', $this->getYoutubeLink(), $matches, PREG_UNMATCHED_AS_NULL);
        return 1 === $result ? $matches['HASH'] : null;
    }

    public function getYoutubeLink(): string
    {
        return $this->youtube ?? '';
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getWeight(): float
    {
        return (float)$this->weight;
    }

    public function getVolume(): float
    {
        return (float)$this->volume;
    }

    public function isFinalWeight(): bool
    {
        return $this->weightIsFinal;
    }

    public function isFinalVolume(): bool
    {
        return $this->volumeIsFinal;
    }
}
