<?php

declare(strict_types=1);

namespace App\Model\File\UseCase\Category\Add;

use App\Model\Cpanel\Repository\CategoryDealerRepository;
use App\Model\File\Entity\FileCategory;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private $em;

    private $categoryDealer;

    public function __construct(EntityManagerInterface $em, CategoryDealerRepository $categoryDealer)
    {
        $this->em = $em;
        $this->categoryDealer = $categoryDealer;
    }

    public function handle(Command $command): void
    {
        $fileInCategory = new FileCategory();
        $fileInCategory
            ->setCategory($this->categoryDealer->findById($command->category))
            ->setFile($command->file);

        $this->em->persist($fileInCategory);
        $fileInCategory->setPosition($fileInCategory->getId());
        $this->em->flush();
    }
}
