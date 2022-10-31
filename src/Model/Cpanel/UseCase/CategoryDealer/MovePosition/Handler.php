<?php

declare(strict_types=1);

namespace App\Model\Cpanel\UseCase\CategoryDealer\MovePosition;

use App\Model\Cpanel\Repository\CategoryDealerRepository;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private $em;
    private $categories;

    public function __construct(CategoryDealerRepository $categories, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->categories = $categories;
    }

    public function handle(Command $command): void
    {
        $currentPosition = $command->currentCategory->getPosition();
        $friendlyCategory = $this->categories->findByPosition($currentPosition, $command->direction);

        if (!$friendlyCategory) {
            throw new \DomainException('position.extreme');
        }

        $command->currentCategory->setPosition($friendlyCategory->getPosition());
        $friendlyCategory->setPosition($currentPosition);
        $this->em->persist($command->currentCategory);
        $this->em->persist($friendlyCategory);
        $this->em->flush();
    }
}
