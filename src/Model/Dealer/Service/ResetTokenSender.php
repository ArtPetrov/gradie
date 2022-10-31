<?php

declare(strict_types=1);

namespace App\Model\Dealer\Service;

use App\Model\Dealer\Entity\ResetToken;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\NamedAddress;
use Symfony\Component\Mime\Email;

class ResetTokenSender
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(string $email, ResetToken $token, string $name = null): void
    {
        $mail = (new TemplatedEmail())
            ->from(new NamedAddress($_ENV['MAILER_FROM_EMAIL'], $_ENV['MAILER_FROM_NAME']));
        if ($name) {
            $mail->to(new NamedAddress($email, $name));
        } else {
            $mail->to($email);
        }

        $mail->priority(Email::PRIORITY_HIGH)
            ->subject('Восстановить пароль от кабинета дилеров')
            ->htmlTemplate('mail/reset_password.html.twig')
            ->context([
                'token' => $token->getToken(),
            ]);

        $this->mailer->send($mail);

    }
}
