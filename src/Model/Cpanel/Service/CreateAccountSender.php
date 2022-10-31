<?php

declare(strict_types=1);

namespace App\Model\Cpanel\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\NamedAddress;
use Symfony\Component\Mime\Email;

class CreateAccountSender
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }


    public function send(string $email, string $password, string $name = null): void
    {
        $mail = (new TemplatedEmail())
            ->from(new NamedAddress($_ENV['MAILER_FROM_EMAIL'], $_ENV['MAILER_FROM_NAME']));
        if ($name) {
            $mail->to(new NamedAddress($email, $name));
        } else {
            $mail->to($email);
        }

        $mail->priority(Email::PRIORITY_HIGH)
            ->subject('Регистрация в сети дилеров')
            ->htmlTemplate('mail/create_account.html.twig')
            ->context([
                'password' => $password,
            ]);

        $this->mailer->send($mail);

    }
}
