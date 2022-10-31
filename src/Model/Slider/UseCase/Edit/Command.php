<?php

declare(strict_types=1);

namespace App\Model\Slider\UseCase\Edit;

use App\Model\Slider\Entity\Slider;
use App\Model\Slider\UseCase\Button;
use App\Model\Slider\UseCase\Info;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public $id;

    public $enable;

    public $type;

    /**
     * @Assert\Valid()
     */
    public $info;

    /**
     * @Assert\Valid()
     */
    public $button;

    public $cover;
    public $prevCover;
    public function __construct(Slider $slider)
    {
        $this->id = $slider->getId();
        $this->enable = $slider->isEnable();
        $this->type = $slider->getType()->getType();

        $this->info = Info\Command::fromSlider($slider->getInfo());
        $this->button = Button\Command::fromSlider($slider->getButton());

        $this->prevCover = $slider->getCover();
    }
}
