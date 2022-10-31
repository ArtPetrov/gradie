<?php

declare(strict_types=1);

namespace App\Model\Cpanel\UseCase\Manager\Edit;

use App\Model\Cpanel\Repository\ManagerRepository;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private $em;
    private $managers;

    public function __construct(ManagerRepository $managers, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->managers = $managers;
    }

    public function handle(Command $command): void
    {
        $manager = $this->managers->findOneBy(['id' => $command->id]);
        $manager->setName($command->name);
        $manager->setEmail($command->email);
        $manager->setPhone($command->phone);
        $this->em->persist($manager);
        $this->em->flush();
    }
}
