<?php

declare(strict_types=1);

namespace App\Model\Quiz\Entity;

use App\Model\EventBus;
use App\Model\EventBusTrait;
use App\Model\File\Entity\File;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="quiz_quest_value")
 */
class QuestValue implements EventBus
{
    use EventBusTrait;

    public const DIRECTORY_FILES = 'quest_value';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Quest
     * @ORM\ManyToOne(targetEntity="Quest", inversedBy="values")
     * @ORM\JoinColumn(name="Quest", onDelete="CASCADE", nullable=false)
     */
    private $quest;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $value;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isMedia = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $style;

    /**
     * @ORM\OneToOne(targetEntity="App\Model\File\Entity\File", cascade = {"persist","remove"}, orphanRemoval=true, fetch="EAGER")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $cover;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    public static function create(
        Quest $quest,
        ?string $val = null,
        ?string $title = null,
        ?string $style = null
    ): self
    {
        $value = new self();
        $value->quest = $quest;
        $value->value = $val;
        $value->title = $title;
        $value->isMedia = false;
        $value->style = $style;
        return $value;
    }

    public function getQuest(): Quest
    {
        return $this->quest;
    }

    public function getCover(): ?File
    {
        return $this->cover;
    }

    public function reloadCover(File $file): self
    {
        $this->cover = $file;
        return $this;
    }

    public static function createMedia(
        Quest $quest,
        File $cover,
        ?string $val = null,
        ?string $title = null,
        ?string $style = null
    ): self
    {
        $value = new self();
        $value->quest = $quest;
        $value->cover = $cover;
        $value->value = $val;
        $value->title = $title;
        $value->isMedia = true;
        $value->style = $style;
        return $value;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function changeTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function changeValue(?string $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getStyle(): ?string
    {
        return $this->style;
    }

    public function changeStyle(?string $style): self
    {
        $this->style = $style;
        return $this;
    }
}
