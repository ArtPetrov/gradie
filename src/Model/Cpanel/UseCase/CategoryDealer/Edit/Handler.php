<?php

declare(strict_types=1);

namespace App\Model\Cpanel\UseCase\CategoryDealer\Edit;

use App\Model\Cpanel\Repository\CategoryDealerRepository;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private $categories;
    private $em;

    public function __construct(CategoryDealerRepository $categories, EntityManagerInterface $em)
    {
        $this->categories = $categories;
        $this->em = $em;
    }

    public function handle(Command $command): void
    {
        $category = $this->categories->findOneBy(['id' => $command->id]);
        $category->setName($command->name);
        $this->em->persist($category);
        $this->em->flush();
    }
}
