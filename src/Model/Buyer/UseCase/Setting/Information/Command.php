<?php

declare(strict_types=1);

namespace App\Model\Buyer\UseCase\Setting\Information;

use App\Model\Buyer\Entity\Buyer;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var int
     * @Assert\NotBlank()
     */
    public $id;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $name;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $phone;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;

    public static function fromBuyer(Buyer $buyer): self
    {
        $command = new self();
        $command->id = $buyer->getId();
        $command->phone = $buyer->getInformation()->getPhone();
        $command->email = $buyer->getInformation()->getEmail();
        $command->name = $buyer->getInformation()->getName();
        return $command;
    }
}
