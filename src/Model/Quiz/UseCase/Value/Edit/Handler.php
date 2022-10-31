<?php

declare(strict_types=1);

namespace App\Model\Quiz\UseCase\Value\Edit;

use App\Model\File\Service\Uploader;
use App\Model\Flusher;
use App\Model\Quiz\Entity\QuestValue;
use App\Model\Quiz\Repository\ValueRepository;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private $flusher;
    private $values;
    private $uploader;
    private $em;

    public function __construct(
        Flusher $flusher,
        ValueRepository $values,
        Uploader $uploader,
        EntityManagerInterface $em)
    {
        $this->flusher = $flusher;
        $this->values = $values;
        $this->uploader = $uploader;
        $this->em = $em;
    }

    public function handle(Command $command): void
    {
        $value = $this->values->getById((int)$command->id);
        $value
            ->changeValue($command->value)
            ->changeTitle($command->name)
            ->changeStyle($command->style);

        if ($command->cover) {
            $this->em->remove($value->getCover());
            $file = $this->uploader->upload($command->cover, QuestValue::DIRECTORY_FILES, true);
            $value->reloadCover($file);
        }

        $this->flusher->flush($value);
    }
}
