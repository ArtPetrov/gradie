<?php

declare(strict_types=1);

namespace App\Model\Quiz\Entity;

use App\Model\EventBus;
use App\Model\EventBusTrait;
use App\Model\File\Entity\File;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Liip\ImagineBundle\Service\FilterService;

/**
 * @ORM\Entity
 * @ORM\Table(name="quiz")
 */
class Quiz implements EventBus
{
    use EventBusTrait;

    public const DIRECTORY_FILES = 'quiz';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enable = true;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $textBegin;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $textEnd;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\OneToOne(targetEntity="App\Model\File\Entity\File", cascade = {"persist","remove"}, orphanRemoval=true, fetch="EAGER")
     * @ORM\JoinColumn(name="cover_id", referencedColumnName="id", nullable=true)
     */
    private $cover;

    /**
     * @var ArrayCollection|Quest[]
     * @ORM\OneToMany(targetEntity="Quest", mappedBy="quiz", orphanRemoval=true, cascade={"persist"})
     */
    private $quests;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $map;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    private function __construct()
    {
        $this->quests = new ArrayCollection();
    }

    public static function create(
        string $name,
        bool $enable,
        string $textBegin,
        string $textEnd,
        string $textContent = null
    ): self
    {
        $quiz = (new self())
            ->rename($name)
            ->changeTextBegin($textBegin)
            ->changeTextEnd($textEnd)
            ->editContent($textContent)
            ->disable();
        $quiz->enable = $enable;
        return $quiz;
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

    public function rename(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function isEnable(): bool
    {
        return $this->enable;
    }

    public function enable(): self
    {
        $this->enable = true;
        return $this;
    }

    public function disable(): self
    {
        $this->enable = false;
        return $this;
    }

    public function changeTextBegin(string $text): self
    {
        $this->textBegin = $text;
        return $this;
    }

    public function getTextBegin(): string
    {
        return $this->textBegin;
    }

    public function changeTextEnd(string $text): self
    {
        $this->textEnd = $text;
        return $this;
    }

    public function getTextEnd(): string
    {
        return $this->textEnd;
    }

    public function editContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getQuests(): array
    {
        return $this->quests->toArray();
    }

    public function getQuestsSource(FilterService $filterImages): array
    {
        $list = [];
        /** @var Quest $quest */
        foreach ($this->getQuests() as $quest) {

            $val = [];
            /** @var QuestValue $value */
            foreach ($quest->getValues() as $value) {
                $vals = [
                    'id' => $value->getId(),
                    'name' => $value->getTitle(),
                    'value' => $value->getValue(),
                    'style' => $value->getStyle(),
                    'cover' => []
                ];
                if ($quest->getType()->isMedia()) {
                    $vals['cover']['small'] = $filterImages->getUrlOfFilteredImage($value->getCover()->getPath(), 'quiz_small');
                    $vals['cover']['big'] = $filterImages->getUrlOfFilteredImage($value->getCover()->getPath(), 'quiz_big');
                }

                $val[$value->getId()] = $vals;
            }

            $list[$quest->getId()] = [
                'id' => $quest->getId(),
                'values' => $val,
                'name' => $quest->getName(),
                'quest' => $quest->getQuest(),
                'help' => $quest->getHelp(),
                'isVariable' => $quest->isVariable(),
                'isAnotherAnswer' => $quest->supportAnotherAnswer(),
                'isSkip' => $quest->isSkip(),
                'isMedia' => $quest->getType()->isMedia(),
                'type' => $quest->getType()->getType(),
            ];
        }

        return $list;
    }

    public function getMap(): ?array
    {
        return $this->map;
    }

    public function saveMap(array $map): self
    {
        $this->map = $map;
        return $this;
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
