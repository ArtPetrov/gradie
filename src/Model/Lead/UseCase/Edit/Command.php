<?php

declare(strict_types=1);

namespace App\Model\Lead\UseCase\Edit;

use App\Model\Lead\Entity\Lead;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{

    public $id;

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

    public static function fromLead(Lead $lead): self
    {
        $command = new self();
        $command->id = $lead->getId();
        $command->name = $lead->getClient()->getName();
        $command->city = $lead->getClient()->getCity();
        $command->email = $lead->getClient()->getEmail();
        $command->phone = $lead->getClient()->getPhone();
        return $command;
    }
}

