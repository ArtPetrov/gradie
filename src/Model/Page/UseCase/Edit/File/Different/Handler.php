<?php

declare(strict_types=1);

namespace App\Model\Page\UseCase\Edit\File\Different;

use App\Model\File\Entity\File;
use App\Model\File\Repository\FileRepository;
use App\Model\File\Repository\FileTemporaryRepository;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private $files;
    private $tmpFiles;
    private $em;

    public function __construct(EntityManagerInterface $em, FileRepository $files, FileTemporaryRepository $tmpFiles)
    {
        $this->tmpFiles = $tmpFiles;
        $this->files = $files;
        $this->em = $em;
    }

    public function handle(Command $command): void
    {
        $oldFiles = array_flip(array_map(static function (File $file): int {
            return $file->getId();
        }, $command->oldFiles));

        /** @var File $new */
        /** @var File $old */
        foreach ($command->currentFiles as $new) {
            foreach ($command->oldFiles as $old) {
                if ($new->getId() === $old->getId()) {
                    unset($oldFiles[$old->getId()]);
                    continue 2;
                }
            }
            $this->tmpFiles->removeByFile($new);
        }

        foreach ($command->oldFiles as $old) {
            if (array_key_exists($old->getId(), $oldFiles)) {
                $this->em->remove($old);
            }
        }
        $this->em->flush();
    }
}
