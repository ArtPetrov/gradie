<?php

declare(strict_types=1);

namespace App\Model\Ticket\UseCase\Ticket\Reply;

use App\Model\File\Service\Uploader;
use App\Model\Ticket\Entity\Message\Files;
use App\Model\Ticket\Entity\Message\Message;
use App\Model\Ticket\Service\ReplySupportSender;
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
    /**
     * @var ReplySupportSender
     */
    private $sender;

    public function __construct(EntityManagerInterface $em, Uploader $uploader, ReplySupportSender $sender)
    {
        $this->em = $em;
        $this->uploader = $uploader;
        $this->sender = $sender;
    }

    public function handle(Command $command): void
    {
        if ($command->ticket->status()->isClosed()) {
            throw new \DomainException('ticket.closed.no.ask');
        }

        $message = new Message();
        $message->setContent($command->content);
        $message->type()->question();
        $message->setSupport($command->support);

        if ($command->file) {
            $attachment = $this->uploader->upload($command->file, 'ticket', false);
            $message->addFile(new Files($attachment));
        }
        $command->ticket->sendMessage($message);
        $command->ticket->state()->supportReply();
        $this->em->persist($command->ticket);

        $this->sender->send($command->ticket);

        $this->em->flush();

    }
}
