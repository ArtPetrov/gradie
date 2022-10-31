<?php

declare(strict_types=1);

namespace App\Model\Slider\Entity;

use App\Model\EventBus;
use App\Model\EventBusTrait;
use App\Model\File\Entity\File;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="sliders",
 * indexes={
 *     @ORM\Index(name="sliders_enable", columns={"type", "enable"}),
 *     @ORM\Index(name="sliders_position", columns={"type", "position"}),
 * })
 */
class Slider implements EventBus
{
    use EventBusTrait;

    public const DIRECTORY_FILES = 'sliders';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Embedded(class="Information", columnPrefix="slider_")
     */
    private $info;

    /**
     * @ORM\Embedded(class="Button", columnPrefix="button_")
     */
    private $button;

    /**
     * @ORM\Embedded(class="Type", columnPrefix=false)
     */
    private $type;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $enable;

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

    public static function create(Information $info, Button $button, Type $type): self
    {
        $slider = new self();
        $slider
            ->changeInfo($info)
            ->changeButton($button)
            ->changeType($type);
        return $slider;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getInfo(): Information
    {
        return $this->info;
    }

    public function changeInfo(Information $info): self
    {
        $this->info = $info;
        return $this;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function changeType(Type $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getButton(): Button
    {
        return $this->button;
    }

    public function changeButton(Button $button): self
    {
        $this->button = $button;
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

    public function changeState(bool $status): self
    {
        $this->enable = $status;
        return $this;
    }

    public function isEnable(): bool
    {
        return $this->enable;
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
