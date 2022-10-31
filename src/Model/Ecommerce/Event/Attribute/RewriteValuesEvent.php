<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Event\Attribute;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Contracts\EventDispatcher\Event;

class RewriteValuesEvent extends Event
{
    private $id;
    private $newValues;
    private $oldValues;

    public function __construct(int $id, ArrayCollection $newValues, ArrayCollection $oldValues)
    {
        $this->id = $id;
        $this->oldValues = $oldValues;
        $this->newValues = $newValues;
    }

    public function oldValues(): ArrayCollection
    {
        return $this->oldValues;
    }

    public function newValues(): ArrayCollection
    {
        return $this->newValues;
    }

    public function getId(): int
    {
        return $this->id;
    }
}