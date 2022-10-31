<?php

declare(strict_types=1);

namespace App\Model\Mailer\UseCase\Create;

use App\Model\Mailer\Entity\Mailer;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="6")
     */
    public $name;
    /**
     * @Assert\NotBlank()
     */
    public $type;

    /**
     * @var Mail
     * @Assert\NotBlank()
     * @Assert\Valid()
     */
    public $mail;

    /**
     * @var Recipient
     * @Assert\NotBlank()
     */
    public $recipient;

    /**
     * @var Sender
     * @Assert\NotBlank()
     */
    public $sender;

    public function __construct(?string $email = null)
    {
        $this->recipient = new Recipient($email);
        $this->mail = new Mail();
        $this->type = $email ? Mailer::TYPE_MAIL : Mailer::TYPE_MAILING;
        $this->sender = new Sender('no-reply@gardie-design.com', 'Gardie - гардеробные системы');

        if ($email) {
            $this->name = sprintf("Письмо для %s", $email);
        }
    }

}
