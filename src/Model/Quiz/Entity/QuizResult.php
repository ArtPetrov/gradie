<?php

declare(strict_types=1);

namespace App\Model\Quiz\Entity;

use App\Model\EventBus;
use App\Model\EventBusTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="quiz_result")
 */
class QuizResult implements EventBus
{
    use EventBusTrait;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Quiz
     * @ORM\ManyToOne(targetEntity="Quiz")
     * @ORM\JoinColumn(name="Quiz", onDelete="CASCADE", nullable=false)
     */
    private $quiz;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $answers;

    /**
     * @var Status
     * @ORM\Embedded(class="Status", columnPrefix=false)
     */
    private $status;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    public static function create(
        Quiz $quiz,
        $answers,
        string $name = null,
        string $email = null,
        string $phone = null
    ): self
    {
        $answer = new self();
        $answer->status = new Status(Status::NEW);
        $answer->quiz = $quiz;
        $answer->name = $name;
        $answer->email = $email;
        $answer->phone = $phone;
        $answer->answers = $answers;

        return $answer;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getQuiz(): Quiz
    {
        return $this->quiz;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getAnswers(): ?array
    {
        return $this->answers;
    }

    public function changeStatus(Status $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }
}
