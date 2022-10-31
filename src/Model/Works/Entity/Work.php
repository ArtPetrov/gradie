<?php

declare(strict_types=1);

namespace App\Model\Works\Entity;

use App\Model\EventBus;
use App\Model\EventBusTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity()
 * @ORM\EntityListeners({"App\Model\Works\EventListener\WorkListener"})
 * @ORM\Table(name="content_works",
 * indexes={
 *     @ORM\Index(name="work_pos_idx", columns={"position"}),
 * })
 */
class Work implements EventBus
{
    use EventBusTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Embedded(class="Content", columnPrefix=false)
     */
    private $content;

    /**
     * @ORM\Embedded(class="Seo", columnPrefix="seo_")
     */
    private $seo;

    /**
     * @ORM\OneToMany(targetEntity="Image", mappedBy="work", orphanRemoval=true, cascade={"persist","remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="work_id")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity="ImageDiy", mappedBy="work", orphanRemoval=true, cascade={"persist","remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="work_id")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $diy;

    /**
     * @ORM\OneToMany(targetEntity="Composition", mappedBy="work", orphanRemoval=true, cascade={"persist","remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="product_id")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $composition;

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
        $this->composition = new ArrayCollection();
        $this->diy = new ArrayCollection();
    }

    public static function create(Content $content, Seo $seo): self
    {
        $work = new self();
        $work->setContent($content);
        $work->updateSeo($seo);
        return $work;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getVersion(): int
    {
        return $this->version;
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

    public function getContent(): Content
    {
        return $this->content;
    }

    public function setContent(Content $content): self
    {
        $this->content = $content;
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

    public function addInComposition(Composition $product): self
    {
        if ($this->composition->contains($product)) {
            return $this;
        }
        $this->composition->add($product);
        return $this;
    }

    public function getComposition(): array
    {
        return $this->composition->toArray();
    }

    public function removeFromComposition(Composition $product): self
    {
        $this->composition->removeElement($product);
        return $this;
    }

    public function addDiy(ImageDiy $image): self
    {
        if ($this->diy->contains($image)) {
            return $this;
        }
        $this->diy->add($image);
        return $this;
    }

    public function getDiys(): array
    {
        return $this->diy->toArray();
    }

    public function removeDiy(ImageDiy $image): self
    {
        $this->diy->removeElement($image);
        return $this;
    }
}
