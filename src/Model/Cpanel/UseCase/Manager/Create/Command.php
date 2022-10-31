<?php

declare(strict_types=1);

namespace App\Model\Cpanel\UseCase\Manager\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="2")
     */
    public $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="4")
     */
    public $phone;

}
