<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Ordering\Address;

class ShippingPrice
{
    public function calculate(Command $command): ?float
    {
        $baseNamespace = 'App\Model\Order\UseCase\Ordering\Address\Cities\\' . $command->{$command->type}::TYPE;
        $shippingCalc = $baseNamespace . '\Price';
        $shippingCost = (new $shippingCalc())->getCostShipping($command->{$command->type}, $command->orderPriceWithPromo);
        return $shippingCost;
    }
}
