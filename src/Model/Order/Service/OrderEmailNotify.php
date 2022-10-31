<?php

declare(strict_types=1);

namespace App\Model\Order\Service;

use App\Controller\ErrorHandler;
use App\Model\Order\Entity\Order;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\NamedAddress;
use Symfony\Component\Mime\Email;

class OrderEmailNotify
{
    private $mailer;
    private $errors;

    public function __construct(
        MailerInterface $mailer,
        ErrorHandler $errors
    )
    {
        $this->errors = $errors;
        $this->mailer = $mailer;
    }

    public function forBuyers(Order $order): void
    {
        $mail = (new TemplatedEmail())
            ->from(new NamedAddress($_ENV['MAILER_FROM_EMAIL'], $_ENV['MAILER_FROM_NAME']));
        if ($order->getContact()->getName()) {
            $mail->to(new NamedAddress($order->getContact()->getEmail(), $order->getContact()->getName()));
        } else {
            $mail->to($order->getContact()->getName());
        }

        $mail->priority(Email::PRIORITY_HIGH)
            ->subject('Ваш заказ №' . $order->getId() . ' сформирован')
            ->htmlTemplate('mail/order/new.html.twig')
            ->context([
                'order' => $order,
            ]);

        $this->mailer->send($mail);
    }
}
