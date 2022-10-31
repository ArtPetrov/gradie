<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Ordering\Address\Cities\Moscow;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public const TYPE = 'Moscow';

    /**
     * @Assert\Length(max=64)
     */
    public $city;

    /**
     * @Assert\Length(max=255)
     */
    public $address;

    /**
     * @Assert\Positive()
     */
    public $howManyKm = 50;

    public $deliveryTo = 'mkad';

    public $baseCostShipping = 500;
    public $freeShippingLimit = 30000;
    public $costKmShipping = 30;

    public static function fromJson(array $fields):self
    {
        $command = new self();
        $command->city = $fields['city'];
        $command->address = $fields['address'];
        $command->howManyKm = $fields['howManyKm']??0;
        $command->deliveryTo = $fields['deliveryTo'];
        return $command;
    }
}
