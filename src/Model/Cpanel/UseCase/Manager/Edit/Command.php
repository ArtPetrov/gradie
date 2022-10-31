<?php

declare(strict_types=1);

namespace App\Model\Cpanel\UseCase\Manager\Edit;

use App\Model\Cpanel\Entity\Manager;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $id;

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

    public function __construct($id)
    {
        $this->id = $id;
    }

    public static function fromManager(Manager $manager): self
    {
        $command = new self($manager->getId());
        $command->email = $manager->getEmail();
        $command->name = $manager->getName();
        $command->phone = $manager->getPhone();
        return $command;
    }
}
