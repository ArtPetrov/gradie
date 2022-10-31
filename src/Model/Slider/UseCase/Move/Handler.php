<?php

declare(strict_types=1);

namespace App\Model\Slider\UseCase\Move;

use App\Model\Flusher;
use App\Model\Slider\ReadModel\SlidersFetcher;

class Handler
{
    private $flusher;
    private $sliders;

    public function __construct(Flusher $flusher, SlidersFetcher $sliders)
    {
        $this->flusher = $flusher;
        $this->sliders = $sliders;
    }

    public function handle(Command $command): void
    {
        $slider = $this->sliders->get($command->id);
        $currentPosition = $slider->getPosition();
        $friendlySliderId = $this->sliders->findByPosition($currentPosition, $slider->getType(), $command->direction);

        if ($friendlySliderId === 0) {
            throw new \DomainException('position.extreme');
        }

        $friendlyWork = $this->sliders->get($friendlySliderId);

        $slider->setPosition($friendlyWork->getPosition());
        $friendlyWork->setPosition($currentPosition);
        $this->flusher->flush();
    }
}
