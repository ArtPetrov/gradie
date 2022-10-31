<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Ordering\Address\Cities\Regions;

class Price
{
    public function getCostShipping(Command $command, float $totalCost): float
    {
        $rule = new Command();
        $cost = 0.00;
        if ($command->freeShippingLimit > $totalCost) {
            $cost += $rule->baseCostShipping;
        }
        return $cost;
    }
}
