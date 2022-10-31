<?php

declare(strict_types=1);

namespace App\Model\PopularProducts\Entity;

use App\Model\EventBus;
use App\Model\EventBusTrait;
use App\Model\File\Entity\File;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="popular_products",
 * indexes={
 *     @ORM\Index(name="popular_products_position", columns={"position"}),
 * })
 */
class PopularProducts implements EventBus
{
    use EventBusTrait;

    public const DIRECTORY_FILES = 'popular_products';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $header;

    /**
     * @ORM\Column(type="string", length=12)
     */
    private $price;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $link;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @ORM\OneToOne(targetEntity="App\Model\File\Entity\File", cascade = {"persist","remove"}, orphanRemoval=true, fetch="EAGER")
     * @ORM\JoinColumn(name="cover_id", referencedColumnName="id", nullable=true)
     */
    private $cover;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    private function __construct()
    {
        $this->enable = true;
    }

    public static function create(string $header, string $link, string $price): self
    {
        $product = new self();
        $product
            ->changeHeader($header)
            ->changeLink($link)
            ->updatePrice($price);
        return $product;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getHeader(): string
    {
        return $this->header;
    }

    public function changeHeader(string $header): self
    {
        $this->header = $header;
        return $this;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function updatePrice(string $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function changeLink(string $link): self
    {
        $this->link = $link;
        return $this;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function uploadCover(File $file): self
    {
        $this->cover = $file;
        return $this;
    }

    public function getCover(): ?File
    {
        return $this->cover;
    }
}
