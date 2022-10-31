<?php

declare(strict_types=1);

namespace App\Model\Slider\UseCase\Create;

use App\Model\File\Service\Uploader;
use App\Model\Flusher;
use App\Model\Slider\Entity\Button;
use App\Model\Slider\Entity\Information;
use App\Model\Slider\Entity\Type;
use App\Model\Slider\Entity\Slider;
use App\Model\Slider\Repository\SlidersRepository;

class Handler
{
    private $flusher;
    private $sliders;
    private $uploader;

    public function __construct(Flusher $flusher, SlidersRepository $sliders, Uploader $uploader)
    {
        $this->flusher = $flusher;
        $this->sliders = $sliders;
        $this->uploader = $uploader;
    }

    public function handle(Command $command): void
    {
        $type = new Type($command->type);
        $info = new Information($command->info->header, $command->info->description);
        $button = new Button($command->button->enable, $command->button->label, $command->button->link);
        $slider = Slider::create($info, $button, $type);

        if ($type->isIndexSlider()) {
            if (!$command->cover) {
                throw new \DomainException('cover.not.upload');
            }

            $file = $this->uploader->upload($command->cover, Slider::DIRECTORY_FILES, true);
            $slider->uploadCover($file);
        }

        $this->sliders->add($slider);
        $this->flusher->flush($slider);
    }
}

