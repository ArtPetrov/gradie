<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Ordering\Address\Cities\Regions;


class Handler
{
    public function handle(Command $command): array
    {
        if (!$command->city || 64 < mb_strlen($command->city)) {
            throw new \DomainException('order.error.city');
        }

        if (!$command->transportCompany || 64 < mb_strlen($command->transportCompany)) {
            throw new \DomainException('order.error.transport.company');
        }

        return [
            'city' => $command->city,
            'transportCompany' => $command->transportCompany,
            'address' => $command->address,
            'deliveryTo' => $command->deliveryTo,
            'tightPacking' => $command->tightPacking,
            'insurance' => $command->insurance
        ];
    }
}
