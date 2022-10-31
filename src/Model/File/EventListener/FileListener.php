<?php

declare(strict_types=1);

namespace App\Model\File\EventListener;

use App\Model\File\Entity\File;
use App\Model\File\Service\Uploader;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class FileListener
{
    private $uploader;
    private $cacheManager;

    public function __construct(Uploader $uploader, CacheManager $cacheManager)
    {
        $this->uploader = $uploader;
        $this->cacheManager = $cacheManager;
    }

    public function postRemove(File $file, LifecycleEventArgs $event)
    {
        $this->cacheManager->remove($file->getPath());

        $this->uploader->delete($file);
    }
}