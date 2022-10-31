<?php

declare(strict_types=1);

namespace App\Model\Mailer\Service;

use App\Model\File\Entity\File;
use App\Model\File\Service\Uploader;
use App\Model\Mailer\Entity\Mailer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\NamedAddress;
use Symfony\Component\Mime\Email;

class MailSender
{
    private $sender;
    /**
     * @var Uploader
     */
    private $uploader;

    public function __construct(MailerInterface $sender, Uploader $uploader)
    {
        $this->sender = $sender;
        $this->uploader = $uploader;
    }

    public function send(string $email, Mailer $mailer): void
    {
        if ($mailer->sender()->getName()) {
            $from = new NamedAddress($mailer->sender()->getEmail(), $mailer->sender()->getName());
        } else {
            $from = new Address($mailer->sender()->getEmail());
        }

        $mail = (new TemplatedEmail())->from($from)->to($email);

        if ($mailer->mail()->getFiles()->count() > 0) {
            foreach ($mailer->mail()->getFiles() as $link) {
                /** @var File $file */
                $file = $link->getFile();
                $mail->attach($this->uploader->read($file), $file->getOriginalFilename(), $file->getMimeType());
            }
        }

        if ($mailer->isMailType()) {
            $mail->priority(Email::PRIORITY_HIGH)
                ->htmlTemplate('mail/mailer_mail.html.twig');
        }

        if ($mailer->isMailingType()) {
            $mail->priority(Email::PRIORITY_NORMAL)
                ->htmlTemplate('mail/mailer.html.twig');
        }


        $mail->subject($mailer->mail()->getHeader())
            ->context([
                'content' => $mailer->mail()->getContent(),
            ]);

        $this->sender->send($mail);

    }
}
