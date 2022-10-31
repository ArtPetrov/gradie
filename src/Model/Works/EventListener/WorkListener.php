<?php

declare(strict_types=1);

namespace App\Model\Works\EventListener;

use App\Model\File\Repository\FileTemporaryRepository;
use App\Model\Works\Entity\Work;
use App\Model\Works\Service\SearchImages;
use App\Model\Works\UseCase\Edit\File\Different;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class WorkListener
{
    private $directory;
    private $searchImages;
    private $tempFiles;
    private $em;
    private $diffHandler;
    private $currentFiles = [];

    public function __construct(
        SearchImages $searchImages,
        EntityManagerInterface $em,
        FileTemporaryRepository $tempFiles,
        Different\Handler $diffHandler,
        string $directoryForWYSIWYG
    )
    {
        $this->directory = $directoryForWYSIWYG;
        $this->searchImages = $searchImages;
        $this->tempFiles = $tempFiles;
        $this->em = $em;
        $this->diffHandler = $diffHandler;
    }

    public function postPersist(Work $work, LifecycleEventArgs $event)
    {
        $files = $this->searchImages->getAll($work->getContent()->getContent(), $this->directory);
        foreach ($files as $file) {
            $this->tempFiles->removeByFile($file);
        }
        $this->em->flush();
    }

    public function postUpdate(Work $work, LifecycleEventArgs $event)
    {
        $lastFiles= $this->searchImages->getAll($work->getContent()->getContent(), $this->directory);
        $this->diffHandler->handle(new Different\Command($this->currentFiles, $lastFiles));
    }

    public function preUpdate(Work $work, PreUpdateEventArgs $event)
    {
        if ($event->hasChangedField('content.content')) {
            $this->currentFiles = $this->searchImages->getAll($event->getOldValue('content.content'), $this->directory);
        }
    }

    public function postRemove(Work $work, LifecycleEventArgs $event)
    {
        $files = $this->searchImages->getAll($work->getContent()->getContent(), $this->directory);
        foreach ($files as $file) {
            $this->em->remove($file);
        }
        $this->em->flush();
    }
}
