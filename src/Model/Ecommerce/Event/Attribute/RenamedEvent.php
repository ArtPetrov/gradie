<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Event\Attribute;

use Symfony\Contracts\EventDispatcher\Event;

class RenamedEvent extends Event
{
    private $attributeId;
    private $oldSlug;

    public function __construct(int $attributeId, string $oldSlug)
    {
        $this->oldSlug = $oldSlug;
        $this->attributeId = $attributeId;
    }

    public function oldSlug(): string
    {
        return $this->oldSlug;
    }

    public function getIdAttribute(): int
    {
        return $this->attributeId;
    }
}