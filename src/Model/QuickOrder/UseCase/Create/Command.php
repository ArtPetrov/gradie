<?php

declare(strict_types=1);

namespace App\Model\QuickOrder\UseCase\Create;

class Command
{
    public $product;
    public $count;
    public $name;
    public $contact;

    public static function fromFronted(int $product, int $count, string $name, string $contact): self
    {
        $command = new self();
        $command->product = $product;
        $command->count = $count;
        $command->name = $name;
        $command->contact = $contact;
        return $command;
    }
}
