<?php

declare(strict_types=1);

namespace App\Model\Mailer\UseCase\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Sender
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email
     */
    public $email;

    /**
     * @Assert\Length(min="2")
     */
    public $name;

    public function __construct(string $email, ?string $name = null)
    {
        $this->email = $email;
        $this->name = $name;
    }
}
