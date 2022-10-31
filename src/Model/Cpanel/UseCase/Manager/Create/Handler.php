<?php

declare(strict_types=1);

namespace App\Model\Cpanel\UseCase\Manager\Create;

use App\Model\Cpanel\Entity\Manager;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function handle(Command $command): void
    {
        $manager = new Manager();
        $manager->setName($command->name);
        $manager->setEmail($command->email);
        $manager->setPhone($command->phone);
        $this->em->persist($manager);
        $this->em->flush();
    }
}
