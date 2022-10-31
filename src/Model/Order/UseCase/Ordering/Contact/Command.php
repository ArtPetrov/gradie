<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Ordering\Contact;

use App\Model\Buyer\Entity\Buyer;
use App\Model\Order\Entity\Order;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=64)
     */
    public $name;

    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Email()
     */
    public $email;

    /**
     * @Assert\NotNull(message="not.null")
     * @Assert\Length(max=32)
     */
    public $phone;

    public $uuid = null;

    public static function fromBuyer(Buyer $buyer): self
    {
        $command = new self();
        $command->name = $buyer->getInformation()->getName();
        $command->email = $buyer->getInformation()->getEmail();
        $command->phone = $buyer->getInformation()->getPhone();
        return $command;
    }

    public static function fromOrder(Order $order): self
    {
        $command = new self();
        $command->name = $order->getContact()->getName();
        $command->email = $order->getContact()->getEmail();
        $command->phone = $order->getContact()->getPhone();
        $command->uuid = $order->getUuid();
        return $command;
    }
}
