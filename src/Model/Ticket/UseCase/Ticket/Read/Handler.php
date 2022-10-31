<?php

declare(strict_types=1);

namespace App\Model\Ticket\UseCase\Ticket\Read;

use App\Model\File\Service\Uploader;
use App\Model\Ticket\Entity\Message\Files;
use App\Model\Ticket\Entity\Message\Message;
use App\Model\Ticket\Entity\Ticket\State;
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
        if ($command->author && $command->ticket->state()->isNewForAuthor()) {
            $command->ticket->state()->authorRead();
        }

        if ($command->support && $command->ticket->state()->isNewForSupport()) {
            $command->ticket->state()->supportRead();
        }

        $this->em->persist($command->ticket);
        $this->em->flush();
    }
}
