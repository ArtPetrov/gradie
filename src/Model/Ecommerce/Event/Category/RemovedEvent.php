<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Event\Category;

use Symfony\Contracts\EventDispatcher\Event;

class RemovedEvent extends Event
{
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}