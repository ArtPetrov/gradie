<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Contracts\EventDispatcher\Event;

trait EventBusTrait
{
    private $recordedEvents = [];

    public function recordEvent(Event $event): void
    {
        $this->recordedEvents[] = $event;
    }

    public function releaseEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];
        return $events;
    }
}
