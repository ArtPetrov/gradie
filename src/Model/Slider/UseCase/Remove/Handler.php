<?php

declare(strict_types=1);

namespace App\Model\Slider\UseCase\Remove;

use App\Model\Flusher;
use App\Model\Slider\Repository\SlidersRepository;

class Handler
{
    private $sliders;
    private $flusher;

    public function __construct(SlidersRepository $sliders, Flusher $flusher)
    {
        $this->sliders = $sliders;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $slider = $this->sliders->get($command->id);
        $this->sliders->remove($slider);
        $this->flusher->flush();
    }
}
