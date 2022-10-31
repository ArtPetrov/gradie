<?php

declare(strict_types=1);

namespace App\Model\Slider\UseCase\Edit;

use App\Model\File\Service\Uploader;
use App\Model\Flusher;
use App\Model\Slider\Entity\Button;
use App\Model\Slider\Entity\Information;
use App\Model\Slider\Entity\Slider;
use App\Model\Slider\Repository\SlidersRepository;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private $flusher;
    private $sliders;
    private $uploader;
    private $em;

    public function __construct(Flusher $flusher, EntityManagerInterface $em, SlidersRepository $sliders, Uploader $uploader)
    {
        $this->flusher = $flusher;
        $this->sliders = $sliders;
        $this->uploader = $uploader;
        $this->em = $em;
    }

    public function handle(Command $command): void
    {
        $slider = $this->sliders->get($command->id);
        $slider
            ->changeState((bool)$command->enable)
            ->changeInfo(new Information($command->info->header, $command->info->description))
            ->changeButton(new Button($command->button->enable, $command->button->label, $command->button->link));

        if ($slider->getType()->isIndexSlider() && $command->cover) {
            $file = $this->uploader->upload($command->cover, Slider::DIRECTORY_FILES, true);
            $slider->uploadCover($file);
            $this->em->remove($command->prevCover);
        }

        $this->flusher->flush($slider);
    }
}