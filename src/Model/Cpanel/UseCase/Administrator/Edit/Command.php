<?php

declare(strict_types=1);

namespace App\Model\Cpanel\UseCase\Administrator\Edit;

use App\Model\Cpanel\Entity\Administrator;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $id;

    /**
     * @Assert\Email()
     */
    public $email;

    public $name;

    /**
     * @Assert\Length(min="6")
     */
    public $password;

    /**
     * @Assert\Length(min="6")
     */
    public $repeatPassword;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public static function fromAdministrator(Administrator $administrator): self
    {
        $command = new self($administrator->getId());
        $command->email = $administrator->getEmail();
        $command->name = $administrator->getName();
        return $command;
    }
}
