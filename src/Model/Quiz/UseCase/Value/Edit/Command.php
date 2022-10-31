<?php

declare(strict_types=1);

namespace App\Model\Quiz\UseCase\Value\Edit;

use App\Model\Quiz\Entity\QuestValue;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public $id;

    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255)
     */
    public $name;

    /**
     * @Assert\Length(max=255)
     */
    public $value;

    public $cover;
    public $style;


    public function __construct(QuestValue $value)
    {
        $this->id = $value->getId();
    }

    public static function fromQuestValue(QuestValue $value): self
    {
        $command = new self($value);
        $command->name = $value->getTitle();
        $command->style = $value->getStyle();
        $command->value = $value->getValue();
        return $command;
    }
}
