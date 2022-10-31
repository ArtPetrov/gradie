<?php

declare(strict_types=1);

namespace App\Model\Quiz\Entity;

use App\Model\EventBus;
use App\Model\EventBusTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="quiz_quest")
 */
class Quest implements EventBus
{
    use EventBusTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Embedded(class="QuestType", columnPrefix=false)
     */
    private $type;

    /**
     * @ORM\Column(type="boolean")
     */
    private $skip = true;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $quest;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $help;

    /**
     * @ORM\Column(type="boolean")
     */
    private $anotherAnswer = false;

    /**
     * @var Quiz
     * @ORM\ManyToOne(targetEntity="Quiz", inversedBy="quests")
     * @ORM\JoinColumn(name="Quiz", onDelete="CASCADE", nullable=false)
     */
    private $quiz;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @var ArrayCollection|QuestValue[]
     * @ORM\OneToMany(targetEntity="QuestValue", mappedBy="quest", orphanRemoval=true, cascade={"persist"})
     */
    private $values;

    private function __construct()
    {
        $this->values = new ArrayCollection();
    }

    public static function create(
        Quiz $quiz,
        QuestType $type,
        string $name,
        string $questText,
        string $help = null,
        bool $skip = true,
        bool $anotherAnswer = false
    ): self
    {
        $quest = new self();
        $quest->quiz = $quiz;
        $quest->type = $type;
        $quest
            ->rename($name)
            ->changeQuest($questText)
            ->changeHelp($help)
            ->changeSkip($skip)
            ->changeAnotherAnswer($anotherAnswer);

        return $quest;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getQuiz():Quiz
    {
        return $this->quiz;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getType(): QuestType
    {
        return $this->type;
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

    public function changeQuest(string $quest): self
    {
        $this->quest = $quest;
        return $this;
    }

    public function getQuest(): string
    {
        return $this->quest;
    }

    public function changeHelp(?string $text): self
    {
        $this->help = $text;
        return $this;
    }

    public function getHelp(): ?string
    {
        return $this->help;
    }

    public function isSkip(): bool
    {
        return $this->skip;
    }

    public function changeSkip(bool $value): self
    {
        $this->skip = $value;
        return $this;
    }

    public function supportAnotherAnswer(): bool
    {
        return $this->anotherAnswer;
    }

    public function changeAnotherAnswer(bool $value): self
    {
        $this->anotherAnswer = $value;
        return $this;
    }

    public function getValues(): array
    {
        return $this->values->toArray();
    }

    public function isVariable(): bool
    {
        return count($this->getValues()) > 1
            && !$this->anotherAnswer
            && !$this->isSkip()
            && $this->getType()->isSupportVariable();
    }
}
