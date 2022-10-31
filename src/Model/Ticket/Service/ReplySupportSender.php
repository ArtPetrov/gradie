<?php

declare(strict_types=1);

namespace App\Model\Ticket\Service;

use App\Model\Ticket\Entity\Ticket\Ticket;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\NamedAddress;
use Symfony\Component\Mime\Email;

class ReplySupportSender
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(Ticket $ticket): void
    {
        $mail = (new TemplatedEmail())
            ->from(new NamedAddress($_ENV['MAILER_FROM_EMAIL'], $_ENV['MAILER_FROM_NAME']));

        $mail->to($ticket->getAuthor()->getEmail());

        $mail->priority(Email::PRIORITY_HIGH)
            ->subject('Служба поддержки ответила на Ваш вопрос')
            ->htmlTemplate('mail/reply_answer.html.twig')
            ->context([
                'ticket' =>$ticket,
            ]);

        $this->mailer->send($mail);

    }
}
