<?php

declare(strict_types=1);

namespace App\Model\Slider\UseCase\Info;

use App\Model\Slider\Entity\Information;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255)
     */
    public $header;

    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=255)
     */
    public $description;

    public static function fromSlider(Information $info): self
    {
        $command = new self();
        $command->header = $info->getHeader();
        $command->description = $info->getDescription();
        return $command;
    }
}
