<?php

declare(strict_types=1);

namespace App\Model\Page\Entity;

use App\Model\EventBus;
use App\Model\EventBusTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\EntityListeners({"App\Model\Page\EventListener\PageListener"})
 * @ORM\Table(name="content_page",
 * indexes={
 *     @ORM\Index(name="slug_idx", columns={"slug"}),
 * })
 */
class Page implements EventBus
{
    use EventBusTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $name;

    /**
     * @ORM\Embedded(class="Content", columnPrefix="content_")
     */
    private $content;

    /**
     * @ORM\Embedded(class="Settings", columnPrefix=false)
     */
    private $settings;

    /**
     * @ORM\Embedded(class="Seo", columnPrefix="seo_")
     */
    private $seo;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    public function __construct(string $name, Content $content, Seo $seo, Settings $settings)
    {
        $this->name = $name;
        $this->content = $content;
        $this->seo = $seo;
        $this->settings = $settings;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function changeName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function updateContent(Content $content): self
    {
        $this->content = $content;
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

    public function getSettings(): Settings
    {
        return $this->settings;
    }

    public function reloadSettings(Settings $settings): self
    {
        $this->settings = $settings;
        return $this;
    }
}
