<?php

declare(strict_types=1);

namespace App\Model\Gallery\Entity;

use App\Model\EventBus;
use App\Model\EventBusTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity()
  * @ORM\Table(name="content_gallery",
 * indexes={
 *     @ORM\Index(name="position_gallery_idx", columns={"position"}),
 * })
 */
class Album implements EventBus
{
    use EventBusTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Embedded(class="Name", columnPrefix="name_")
     */
    private $name;

    /**
     * @ORM\Embedded(class="Seo", columnPrefix="seo_")
     */
    private $seo;

    /**
     * @ORM\OneToMany(targetEntity="Image", mappedBy="album", orphanRemoval=true, cascade={"persist","remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="album_id")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $images;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    private function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public static function create(Name $name, Seo $seo):self
    {
        $album = new self();
        $album->changeName($name);
        $album->updateSeo($seo);
        return $album;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function changeName(Name $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSeo(): Seo
    {
        return $this->seo;
    }

    public function updateSeo(Seo $seo): self
    {
        $this->seo = $seo;
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

    public function addImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            return $this;
        }
        $this->images->add($image);
        return $this;
    }

    public function getImages(): array
    {
        return $this->images->toArray();
    }

    public function removeImage(Image $image): self
    {
        $this->images->removeElement($image);
        return $this;
    }
}
