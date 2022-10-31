<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Ordering\Address\Cities\Pickup;

class Handler
{
    public function handle(Command $command): array
    {
        return [
            'city' => $command->city,
        ];
    }
}
