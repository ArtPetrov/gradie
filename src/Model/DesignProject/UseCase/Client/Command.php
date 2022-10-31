<?php

declare(strict_types=1);

namespace App\Model\DesignProject\UseCase\Client;

use App\Model\DesignProject\Entity\Client;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $name;

    /**
     * @Assert\NotBlank()
     */
    public $phone;

    /**
     * @Assert\NotBlank()
     */
    public $email;

    /**
     * @Assert\NotBlank()
     */
    public $city;

    public static function fromProject(Client $client): self
    {
        $command = new self();
        $command->name = $client->getName();
        $command->phone = $client->getPhone();
        $command->email = $client->getEmail();
        $command->city = $client->getCity();
        return $command;
    }
}
