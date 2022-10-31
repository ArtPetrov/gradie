<?php

declare(strict_types=1);

namespace App\Model\Page\EventListener;

use App\Model\File\Repository\FileTemporaryRepository;
use App\Model\Page\Entity\Page;
use App\Model\Page\Service\SearchImages;
use App\Model\Page\UseCase\Edit\File\Different;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class PageListener
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

    public function postPersist(Page $page, LifecycleEventArgs $event)
    {
        $files = $this->searchImages->getAll($page->getContent()->getBody(), $this->directory);
        foreach ($files as $file) {
            $this->tempFiles->removeByFile($file);
        }
        $this->em->flush();
    }

    public function postUpdate(Page $page, LifecycleEventArgs $event)
    {
        $lastFiles = $this->searchImages->getAll($page->getContent()->getBody(), $this->directory);
        $this->diffHandler->handle(new Different\Command($this->currentFiles, $lastFiles));
    }

    public function preUpdate(Page $page, PreUpdateEventArgs $event)
    {
        if ($event->hasChangedField('content.body')) {
            $this->currentFiles = $this->searchImages->getAll($event->getOldValue('content.body'), $this->directory);
        }
    }

    public function postRemove(Page $page, LifecycleEventArgs $event)
    {
        $files = $this->searchImages->getAll($page->getContent()->getBody(), $this->directory);
        foreach ($files as $file) {
            $this->em->remove($file);
        }
        $this->em->flush();
    }
}
