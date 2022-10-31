<?php

declare(strict_types=1);

namespace App\Model\News\EventListener;

use App\Model\File\Repository\FileTemporaryRepository;
use App\Model\News\Entity\News;
use App\Model\News\Service\SearchImages;
use App\Model\News\UseCase\Edit\File\Different;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class NewsListener
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

    public function postPersist(News $page, LifecycleEventArgs $event)
    {
        $files = $this->searchImages->getAll($page->getContent(), $this->directory);
        foreach ($files as $file) {
            $this->tempFiles->removeByFile($file);
        }
        $this->em->flush();
    }

    public function postUpdate(News $page, LifecycleEventArgs $event)
    {
        $lastFiles = $this->searchImages->getAll($page->getContent(), $this->directory);
        $this->diffHandler->handle(new Different\Command($this->currentFiles, $lastFiles));
    }

    public function preUpdate(News $page, PreUpdateEventArgs $event)
    {
        if ($event->hasChangedField('content')) {
            $this->currentFiles = $this->searchImages->getAll($event->getOldValue('content'), $this->directory);
        }
    }

    public function postRemove(News $page, LifecycleEventArgs $event)
    {
        $files = $this->searchImages->getAll($page->getContent(), $this->directory);
        foreach ($files as $file) {
            $this->em->remove($file);
        }
        $this->em->flush();
    }
}
