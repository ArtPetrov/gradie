<?php

declare(strict_types=1);

namespace App\Model\Article\Entity;

use App\Model\EventBus;
use App\Model\EventBusTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\EntityListeners({"App\Model\Article\EventListener\ArticleListener"})
 * @ORM\Table(name="content_news",
 * indexes={
 *     @ORM\Index(name="pat_article_idx", columns={"published_at"}),
 * })
 */
class Article implements EventBus
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
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    private $publishedAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Embedded(class="Seo", columnPrefix="seo_")
     */
    private $seo;

    /**
     * @ORM\OneToMany(targetEntity="Image", mappedBy="article", orphanRemoval=true, cascade={"persist","remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="news_id")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $images;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    private function __construct()
    {
        $this->publishedAt = new \DateTimeImmutable();
        $this->images = new ArrayCollection();
    }

    public static function create(Name $name, ?string $content, ?\DateTimeInterface $date, Seo $seo): self
    {
        $news = new self();
        $news->setContent($content);
        $news->setPublishedAt($date);
        $news->changeName($name);
        $news->updateSeo($seo);
        return $news;
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

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }
}
