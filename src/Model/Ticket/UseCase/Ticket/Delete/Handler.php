<?php

declare(strict_types=1);

namespace App\Model\Ticket\UseCase\Ticket\Delete;

use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function handle(Command $command): void
    {
        $this->em->remove($command->ticket);
        $this->em->flush();
    }
}
