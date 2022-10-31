<?php

declare(strict_types=1);

namespace App\Model\Mailer\UseCase\File\Delete;

use App\Model\Mailer\Repository\FileRepository;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var FileRepository
     */
    private $fileRepository;


    public function __construct(EntityManagerInterface $em, FileRepository $fileRepository)
    {
        $this->em = $em;
        $this->fileRepository = $fileRepository;
    }

    public function handle(Command $command): void
    {
        $link = $this->fileRepository->findOneBy(['file' => $command->file]);

        if ($link) {
            $this->em->remove($link);
        } else {
            $this->em->remove($command->file);
        }

        $this->em->flush();
    }
}
