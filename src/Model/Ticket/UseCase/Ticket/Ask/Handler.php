<?php

declare(strict_types=1);

namespace App\Model\Ticket\UseCase\Ticket\Ask;

use App\Model\File\Service\Uploader;
use App\Model\Ticket\Entity\Message\Files;
use App\Model\Ticket\Entity\Message\Message;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var Uploader
     */
    private $uploader;

    public function __construct(EntityManagerInterface $em, Uploader $uploader)
    {
        $this->em = $em;
        $this->uploader = $uploader;
    }

    public function handle(Command $command): void
    {
        if($command->ticket->status()->isClosed()){
            throw new \DomainException('ticket.closed.no.ask');
        }

        $message = new Message();
        $message->setContent($command->content);
        $message->type()->question();
        $message->setAuthor($command->author);

        if ($command->file) {
            $attachment = $this->uploader->upload($command->file, 'ticket', false);
            $message->addFile(new Files($attachment));
        }
        $command->ticket->sendMessage($message);
        $command->ticket->state()->authorAsked();
        $this->em->persist($command->ticket);
        $this->em->flush();
    }
}
