<?php

declare(strict_types=1);

namespace App\Model\Ticket\UseCase\Ticket\Close;

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
        if ($command->ticket->status()->isClosed()) {
            throw new \DomainException('ticket.closed');
        }

        if ($command->author) {
            $command->ticket->state()->authorClosed();
        }

        if ($command->support) {
            $command->ticket->state()->supportClosed();
        }

        $command->ticket->status()->closed();

        $this->em->persist($command->ticket);
        $this->em->flush();
    }
}
