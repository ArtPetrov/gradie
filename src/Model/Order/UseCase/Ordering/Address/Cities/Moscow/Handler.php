<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Ordering\Address\Cities\Moscow;

class Handler
{
    public function handle(Command $command): array
    {
        if (!$command->city || 64 < mb_strlen($command->city)) {
            throw new \DomainException('order.error.city');
        }

        if (!$command->address || 255 < mb_strlen($command->address)) {
            throw new \DomainException('order.error.address');
        }

        return [
            'city' => $command->city,
            'address' => $command->address,
            'howManyKm' => $command->howManyKm,
            'deliveryTo' => $command->deliveryTo
        ];
    }
}
