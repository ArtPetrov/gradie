<?php

declare(strict_types=1);

namespace App\Model\Cpanel\UseCase\Administrator\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{

    /**
     * @Assert\Email()
     */
    public $email;

    public $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="6")
     */
    public $password;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="6")
     */
    public $repeatPassword;

}
