<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Event\Attribute;

use Symfony\Contracts\EventDispatcher\Event;

class RemovedEvent extends Event
{
    private $slug;
    private $attributeId;

    public function __construct(int $attributeId, string $slug)
    {
        $this->slug = $slug;
        $this->attributeId = $attributeId;
    }

    public function getId(): int
    {
        return $this->attributeId;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

}