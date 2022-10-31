<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Copy;

use App\Model\Order\Entity\Order;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order->getId();
    }
}
