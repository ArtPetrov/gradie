<?php

declare(strict_types=1);

namespace App\Model\File\UseCase\Category\Position;

use App\Model\Cpanel\Repository\CategoryDealerRepository;
use App\Model\File\Entity\FileCategory;
use App\Model\File\Repository\FileCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private $em;

    private $fileCategoryRepository;

    public function __construct(EntityManagerInterface $em, FileCategoryRepository $fileCategoryRepository)
    {
        $this->em = $em;
        $this->fileCategoryRepository = $fileCategoryRepository;
    }

    public function handle(Command $command): void
    {
        $linkForFiles = $this->fileCategoryRepository->findById($command->category);

        $countItems = count($command->positions)+1;

        foreach ($linkForFiles as $link) {
            $newPosition = $countItems - array_search($link->getFile()->getId(),$command->positions);
            $link->setPosition($newPosition);
            $this->em->persist($link);
        }

        $this->em->flush();
    }
}
