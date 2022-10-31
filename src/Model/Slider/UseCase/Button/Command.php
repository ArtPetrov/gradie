<?php

declare(strict_types=1);

namespace App\Model\Slider\UseCase\Button;

use App\Model\Slider\Entity\Button;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public $enable = false;

    /**
     * @Assert\Length(max=255)
     */
    public $label;

    /**
     * @Assert\Length(max=255)
     */
    public $link;

    public static function fromSlider(Button $button): self
    {
        $command = new self();
        $command->label = $button->getLabel();
        $command->link = $button->getLink();
        $command->enable = $button->isEnable();
        return $command;
    }
}
