<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Ordering\Address\Cities\Pickup;

class Command
{
    public const TYPE = 'Pickup';

    public $city = 'Москва';

    public $baseCostShipping = 0;
    public $freeShippingLimit = 0;

    public static function fromJson(array $fields): self
    {
        $command = new self();
        $command->city = $fields['city'];
        return $command;
    }
}
