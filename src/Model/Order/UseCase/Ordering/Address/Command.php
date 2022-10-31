<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Ordering\Address;

use App\Model\Order\Entity\Order;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotNull(message="not.null")
     */
    public $type = 'moscow';

    public $order = null;

    public $moscow;
    public $regions;
    public $pickup;

    public $orderPrice = 0;
    public $orderPriceWithPromo = 0;

    public function __construct()
    {
        $this->moscow = new Cities\Moscow\Command();
        $this->regions = new Cities\Regions\Command();
        $this->pickup = new Cities\Pickup\Command();
    }

    public static function fromOrderWithOrderPrice(Order $order, float $price, float $priceWithPromo): self
    {
        $command = new self();
        $command->order = $order->getUuid();
        $command->orderPrice = $price;
        $command->orderPriceWithPromo = $priceWithPromo;
        $command->type = $order->getAddress()->getType();
        if ($command->type === 'moscow') {
            $command->moscow = Cities\Moscow\Command::fromJson($order->getAddress()->getFields());
        }
        if ($command->type === 'regions') {
            $command->regions = Cities\Regions\Command::fromJson($order->getAddress()->getFields());
        }
        if ($command->type === 'pickup') {
            $command->pickup = Cities\Pickup\Command::fromJson($order->getAddress()->getFields());
        }
        return $command;
    }
}
